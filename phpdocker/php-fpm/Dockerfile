FROM phpdockerio/php:8.3-fpm
WORKDIR "/home/rija/www/formajax"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.3-gd \
        php8.3-mysql \
        php8.3-xdebug \
        php8.3-yaml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Installer les dépendances nécessaires à Chrome, Panther et PHP
RUN apt-get update && apt-get install -y \
    wget \
    gnupg \
    unzip \
    git \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libx11-xcb1 \
    libxcomposite1 \
    libxcursor1 \
    libxdamage1 \
    libxi6 \
    libxtst6 \
    libnss3 \
    libxrandr2 \
    libasound2 \
    libatk1.0-0 \
    libatk-bridge2.0-0 \
    libcups2 \
    libdrm2 \
    libgbm1 \
    libxshmfence1 \
    fonts-liberation \
    xdg-utils

# Installer Chrome stable
RUN wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | gpg --dearmor > /usr/share/keyrings/google-linux.gpg \
    && echo "deb [arch=amd64 signed-by=/usr/share/keyrings/google-linux.gpg] http://dl.google.com/linux/chrome/deb/ stable main" \
    > /etc/apt/sources.list.d/google-chrome.list \
    && apt-get update && apt-get install -y google-chrome-stable

# Récupérer la version majeure exacte de Chrome (ex: 142)
RUN CHROME_VERSION=$(google-chrome --version | grep -oP '\d+' | head -1) && \
    DRIVER_VERSION=$(wget -qO- "https://googlechromelabs.github.io/chrome-for-testing/LATEST_RELEASE_$CHROME_VERSION") && \
    wget -O /tmp/chromedriver.zip "https://edgedl.me.gvt1.com/edgedl/chrome/chrome-for-testing/${DRIVER_VERSION}/linux64/chromedriver-linux64.zip" && \
    unzip /tmp/chromedriver.zip -d /usr/local/bin/ && \
    mv /usr/local/bin/chromedriver-linux64/chromedriver /usr/local/bin/chromedriver && \
    chmod +x /usr/local/bin/chromedriver && \
    rm -rf /tmp/chromedriver.zip /usr/local/bin/chromedriver-linux64


# Configurer les variables Panther
# ENV PANTHER_CHROMEDRIVER_BINARY=/usr/local/bin/chromedriver
# ENV PANTHER_NO_SANDBOX=1
# ENV PANTHER_CHROME_ARGUMENTS="--headless --no-sandbox --disable-dev-shm-usage --disable-gpu"
