FROM phpdockerio/php:8.3-fpm
WORKDIR "/home/rija/www/formajax"

# PHP extensions
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.3-gd \
        php8.3-mysql \
        php8.3-xdebug \
        php8.3-yaml \
    && apt-get clean \
    && rm -rf /tmp/* /var/tmp/* /usr/share/doc/*

# Dépendances Chrome
RUN apt-get update && apt-get install -y \
    wget gnupg unzip ca-certificates \
    libglib2.0-0 \
    libnss3 \
    libx11-6 \
    libxcomposite1 \
    libxcursor1 \
    libxdamage1 \
    libxext6 \
    libxfixes3 \
    libxi6 \
    libxrandr2 \
    libxrender1 \
    libxtst6 \
    libatk1.0-0 \
    libatk-bridge2.0-0 \
    libcairo2 \
    libdrm2 \
    libpango-1.0-0 \
    libpangocairo-1.0-0 \
    libgbm1 \
    libasound2 \
    fonts-liberation \
    libu2f-udev \
    --no-install-recommends \
    && rm -rf /var/lib/apt/lists/*

# Installer Chrome stable
RUN wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | gpg --dearmor > /usr/share/keyrings/google-linux.gpg \
    && echo "deb [arch=amd64 signed-by=/usr/share/keyrings/google-linux.gpg] http://dl.google.com/linux/chrome/deb/ stable main" \
       > /etc/apt/sources.list.d/google-chrome.list \
    && apt-get update && apt-get install -y google-chrome-stable

# Installer ChromeDriver correspondant
RUN CHROME_VERSION=$(google-chrome --version | grep -oP '\d+' | head -1) && \
    DRIVER_VERSION=$(wget -qO- "https://googlechromelabs.github.io/chrome-for-testing/LATEST_RELEASE_$CHROME_VERSION") && \
    wget -O /tmp/chromedriver.zip "https://edgedl.me.gvt1.com/edgedl/chrome/chrome-for-testing/${DRIVER_VERSION}/linux64/chromedriver-linux64.zip" && \
    unzip /tmp/chromedriver.zip -d /usr/local/bin/ && \
    mv /usr/local/bin/chromedriver-linux64/chromedriver /usr/bin/chromedriver && \
    chmod +x /usr/bin/chromedriver && \
    rm -rf /tmp/chromedriver.zip /usr/local/bin/chromedriver-linux64

# Create non-root user
RUN useradd -m chrome && chown -R chrome:chrome /home/rija/www/formajax
USER chrome
