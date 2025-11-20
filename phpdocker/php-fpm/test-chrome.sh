#!/bin/bash

echo "=== Testing Chrome ==="
google-chrome --version || echo "Chrome not found!"

echo "=== Testing ChromeDriver ==="
chromedriver --version || echo "Chromedriver not found!"

echo "=== Launching Chrome manually ==="
google-chrome \
  --headless=new \
  --disable-gpu \
  --no-sandbox \
  --disable-dev-shm-usage \
  --remote-debugging-port=9222 \
  https://www.google.com/

echo "If you see no error above, Chrome works correctly."
