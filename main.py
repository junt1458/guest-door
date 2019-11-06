import threading
import serial
import time

ser_pause = False
ser_port = "/dev/serial0"
ser_baud = 9600
serial_port = serial.Serial(ser_port, ser_baud, timeout=0)

def ser_received(data):
    print data

def ser_start_reading(ser):
    while True:
        if not ser_pause:
            time.sleep(0.1)
            received = ser.readline()
            if received is not "":
                ser_received(received)

def send_command(cmd):
    ser_pause = True
    serial_port.write(bytes(cmd))
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
    print "Keyboard interrupt."
