# Easy Catalog Images

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/easycatalogimg --prefer-source
bin/magento module:enable Swissup_Core Swissup_Easycatalogimg
bin/magento setup:upgrade
```
