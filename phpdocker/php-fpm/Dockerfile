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

# Chrome dependencies
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

# Install Chrome stable
RUN wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | gpg --dearmor > /usr/share/keyrings/google-linux.gpg \
    && echo "deb [arch=amd64 signed-by=/usr/share/keyrings/google-linux.gpg] http://dl.google.com/linux/chrome/deb/ stable main" \
       > /etc/apt/sources.list.d/google-chrome.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends google-chrome-stable \
    && rm -rf /var/lib/apt/lists/*

# Install matching ChromeDriver using new Chrome for Testing API
RUN set -eux; \
    CHROME_VERSION=$(google-chrome --version | sed -E 's/Google Chrome ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+).*/\1/'); \
    MAJOR=$(echo $CHROME_VERSION | cut -d. -f1); \
    DRIVER_VERSION=$(wget -qO- "https://googlechromelabs.github.io/chrome-for-testing/LATEST_RELEASE_${MAJOR}"); \
    wget -q "https://storage.googleapis.com/chrome-for-testing-public/${DRIVER_VERSION}/linux64/chromedriver-linux64.zip" -O /tmp/chromedriver.zip; \
    unzip /tmp/chromedriver.zip -d /tmp; \
    mv /tmp/chromedriver-linux64/chromedriver /usr/bin/chromedriver; \
    chmod +x /usr/bin/chromedriver; \
    rm -rf /tmp/chromedriver.zip /tmp/chromedriver-linux64
