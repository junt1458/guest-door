#!/usr/bin/env python
# -*- coding: utf8 -*-
import threading
import serial
import time
import datetime
import MySQLdb

import RPi.GPIO as GPIO
import MFRC522

continue_reading = True

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

Reader = MFRC522.MFRC522()
close_countdown = 0
close_working = False
cool_dn = {}

before_card = None
two_time_no = False

print "Guest Door - Ready"

def reading():
    global before_card
    global two_time_no
    while continue_reading:
        (status, TagType) = Reader.MFRC522_Request(Reader.PICC_REQIDL)
        (status, uid) = Reader.MFRC522_Anticoll()
        if status == Reader.MI_OK:
            two_time_no = False
            if before_card != uid:
                before_card = uid
                rd_str = "READ_"
                for uid_ in uid:
                    uid_s = hex(uid_).replace("0x", "").upper()
                    if len(uid_s) == 1:
                        uid_s = "0" + uid_s
                    rd_str = rd_str + uid_s
                print rd_str # need to update. this is wrong with arduino result.
        elif status == 2:
            if two_time_no:
                before_card = None
            else:
                two_time_no = True

def isCoolTime(key_id):
    return cool_dn.has_key(key_id)

def ser_received(data, isIn):
    global ser_pause
    ser_pause = True
    if data.startswith("READ_"):
        card_id = data.replace("READ_", "")
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
            if paused is 0 and (using_key is use_comp or isCoolTime(rec[1])):
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
                    
                    if isCoolTime(rec[1]) is False:
                        global cool_dn
                        cool_dn[rec[1]] = 10
                        ct = threading.Thread(target=cool_del, args=(rec[1],))
                        ct.daemon = True
                        ct.start()
                        new_ct = count - 1
                        if count is not -1 and isIn is false:
                            cursor.execute("UPDATE App_Keys SET use_count=" + new_ct + " WHERE key_id='" + rec[1] + "';")
                        cursor.execute("INSERT INTO Logs (user, action, key_name) VALUES ('" + user + "', " + action + ", '" + key_n + "');")
                        cursor.execute("UPDATE App_Keys SET using_key=" + using_k + " WHERE key_id='" + rec[1] + "';")
                        connection.commit()

    ser_pause = False

def cool_del(key_id):
    global cool_dn
    if key_id in cool_dn:
        val = cool_dn[key_id]
        while val > 0:
            time.sleep(1)
            val = val - 1
        cool_dn.pop(key_id)

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
                ser_received(received, True)

def send_command(cmd):
    send = cmd + '\n'
    ser_pause = True
    serial_port.write(send.encode('utf-8'))
    ser_pause = False

try:
    thread = threading.Thread(target=ser_start_reading, args=(serial_port,))
    thread.daemon = True
    thread.start()
    
    r_thread = threading.Thread(target=reading, args=())
    r_thread.daemon = True
    r_thread.start()
    
    while True:
        command_in = raw_input("")
        if command_in.startswith("I_READ_"):
            ser_received(command_in.replace("I_", ""), True):
        elif command_in.startswith("O_READ_"):
            ser_received(command_in.replace("O_", ""), False):
        else:
            # Manual command input for development.
            send_command(command_in)

except (KeyboardInterrupt, SystemExit):
    ser_pause = True
    serial_port.close()
    connection.close()
    continue_reading = False
    GPIO.cleanup()
    print "Keyboard interrupt."
