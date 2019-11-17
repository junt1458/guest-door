#!/usr/bin/env python
# -*- coding: utf8 -*-
import threading
import serial
import time
import datetime
import MySQLdb

connection = MySQLdb.connect(
    host='192.168.43.29',
    user='test', 
    passwd='TEST_PASSWD',
    db='guest_door'
)

cursor = connection.cursor()

ser_pause = False
ser_port = "/dev/serial0"
ser_baud = 9600
serial_port = serial.Serial(ser_port, ser_baud, timeout=0)

close_countdown = 0
close_working = False
cool_dn_i = {}
cool_dn_o = {}

print "Guest Door - Ready"

def isCoolTime(key_id, isIn):
    if isIn:
        return cool_dn_i.has_key(key_id)
    else:
        return cool_dn_o.has_key(key_id)

def ser_received(data, isIn):
    global ser_pause
    ser_pause = True
    if data.startswith("READ_"):
        card_id = data.replace("READ_", "")
        print "Card %s is detected." % card_id
        cursor.execute("SELECT * FROM App_Keys WHERE key_id='%s';" % card_id)
        result = cursor.fetchall()
        for rec in result:
            user = rec[3]
            count = rec[4]
            key_n = rec[5]
            active_un = rec[7]
            paused = rec[8]
            using_key = rec[9]
            use_comp = 1
            if isIn:
                use_comp = 0
            if paused is 0 and (using_key is use_comp or isCoolTime(rec[1], isIn)):
                if count is -1 or count is not 0 and active_un > datetime.datetime.now():
                    action = "1"
                    using_k = "0"
                    if isIn:
                        action = "0"
                        using_k = "1"
                
                    global close_countdown
                    close_countdown = 5
                    if close_working is False:
                        th = threading.Thread(target=close_ctdwn, args=())
                        th.daemon = True
                        th.start()
                        send_command("ON")
                    global cool_dn_i
                    global cool_dn_o
                    if isCoolTime(rec[1], isIn) is False:
                        if isIn:
                            cool_dn_i[rec[1]] = 10
                        else:
                            cool_dn_o[rec[1]] = 10
                        ct = threading.Thread(target=cool_del, args=(rec[1],isIn,))
                        ct.daemon = True
                        ct.start()
                        new_ct = count - 1
                        if count is not -1 and isIn is False:
                            cursor.execute("UPDATE App_Keys SET use_count=" + str(new_ct) + " WHERE key_id='" + rec[1] + "';")
                        cursor.execute("INSERT INTO Logs (user, action, key_name) VALUES ('" + user + "', " + action + ", '" + key_n + "');")
                        cursor.execute("UPDATE App_Keys SET using_key=" + using_k + " WHERE key_id='" + rec[1] + "';")
                        connection.commit()


    ser_pause = False

def cool_del(key_id, isIn):
    if isIn:
        global cool_dn_i
        if key_id in cool_dn_i:
            val = cool_dn_i[key_id]
            while val > 0:
                time.sleep(1)
                val = val - 1
            cool_dn_i.pop(key_id)
    else:
        global cool_dn_o
        if key_id in cool_dn_o:
            val = cool_dn_o[key_id]
            while val > 0:
                time.sleep(1)
                val = val - 1
            cool_dn_o.pop(key_id)

def close_ctdwn():
    global close_countdown
    global close_working
    close_working = True
    while close_countdown > 0:
        time.sleep(1)
        close_countdown = close_countdown - 1
    time.sleep(1)
    send_command("ON_R")
    close_working = False

def ser_start_reading(ser):
    while True:
        if not ser_pause:
            time.sleep(0.1)
            received = ser.readline()
            if received is not "":
                if received.startswith("I_READ_"):
                    ser_received(received.replace("I_", ""), True)
                elif received.startswith("O_READ_"):
                    ser_received(received.replace("O_", ""), False)

def send_command(cmd):
    send = cmd + '\n'
    ser_pause = True
    serial_port.write(send.encode('utf-8'))
    ser_pause = False

try:
    thread = threading.Thread(target=ser_start_reading, args=(serial_port,))
    thread.daemon = True
    thread.start()
    
    while True:
        command_in = raw_input("")
        if command_in.startswith("I_READ_"):
            ser_received(command_in.replace("I_", ""), True)
        elif command_in.startswith("O_READ_"):
            ser_received(command_in.replace("O_", ""), False)
        else:
            # Manual command input for development.
            send_command(command_in)

except (KeyboardInterrupt, SystemExit):
    ser_pause = True
    connection.close()
    print "Keyboard interrupt."
