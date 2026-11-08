"""
Simple race-condition test for Booking endpoint.

Requires:
    pip install requests

Usage:
    python tests/race_condition_booking.py

It sends 2 concurrent POST requests to /booking/store and prints the responses.
You should observe that at most ONE booking succeeds for the same room/time range
thanks to transaction + SELECT ... FOR UPDATE in BookingService.
"""

import threading
import time
import uuid

import requests

BASE_URL = "http://localhost/doan/public"
BOOKING_URL = f"{BASE_URL}/booking/store"

# Adjust these values to match a valid logged-in session and room in your environment.
ROOM_ID = 1
CHECK_IN = "2026-04-20 12:00"
CHECK_OUT = "2026-04-22 12:00"

# Copy from browser devtools (Cookie header) after logging in as a normal user.
SESSION_COOKIE = "PHPSESSID=YOUR_SESSION_ID_HERE"


def send_booking_request(label: str):
    headers = {
        "Cookie": SESSION_COOKIE,
        "User-Agent": f"RaceTest/{label}",
    }
    data = {
        "room_id": ROOM_ID,
        "check_in": CHECK_IN,
        "check_out": CHECK_OUT,
    }
    try:
        resp = requests.post(BOOKING_URL, data=data, headers=headers, timeout=10)
        print(f"[{label}] status={resp.status_code} url={resp.url}")
        print(f"[{label}] body={resp.text[:300]!r}\n")
    except Exception as e:
        print(f"[{label}] ERROR: {e}")


def main():
    print("=== Race-condition test: 2 concurrent booking requests ===")
    print(f"Endpoint: {BOOKING_URL}")
    print("IMPORTANT: Set SESSION_COOKIE to a valid PHPSESSID of a logged-in user first.\n")

    t1 = threading.Thread(target=send_booking_request, args=("T1",))
    t2 = threading.Thread(target=send_booking_request, args=("T2",))

    t1.start()
    # Very small delay to increase chance of overlap but still concurrent
    time.sleep(0.05)
    t2.start()

    t1.join()
    t2.join()

    print("=== Done. Expectation: at most ONE successful booking, the other should fail with overlap/unavailable. ===")


if __name__ == "__main__":
    main()

