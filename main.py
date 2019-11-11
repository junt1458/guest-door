import threading
import serial
import time
import MySQLdb

connection = MySQLdb.connect(
    host='127.0.0.1',
    user='root',
    passwd='TEST_PASSWD',
    db='guest_door'
)

cursor = connection.cursor()

# do something
# connection.commit() <-- You need to execute this to save changes.

ser_pause = False
ser_port = "/dev/serial0"
ser_baud = 9600
serial_port = serial.Serial(ser_port, ser_baud, timeout=0)

def ser_received(data):
    ser_pause = True
    if data.startswith("READ_"):
        card_id = data.replace("READ_", "")
        cursor.execute("SELECT * FROM App_Keys WHERE key_id=%s", card_id)
        result = cursor.fetchall()
        for rec in result:
            print(rec)
    ser_pause = False

def ser_start_reading(ser):
    while True:
        if not ser_pause:
            time.sleep(0.1)
            received = ser.readline()
            if received is not "":
                ser_received(received)

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
        # Manual command input for development.
        command_in = raw_input("")
        send_command(command_in)

except (KeyboardInterrupt, SystemExit):
    ser_pause = True
    serial_port.close()
    connection.close()
    print "Keyboard interrupt."
