# Privateness network EmerNVS 

## Buy EmerNVS records with NESS, NCH or EMC
## Available on DEX: https://ex.xbts.io/asset/XBTSX.NESS .NCH or .EMC

Execute in a Terminal:

`mv config/config.sample.php config/config.php`

Edit `config/config.php`

### Commands

`php exec/self-test.php [-debug]`

System self-test


`php list-addr.php [wallet-filename]`

List addresses for default (main_wallet_id in config.php) or selected ness wallet


`php exec/new-addr.php [wallet-filename] [new addresses count]`

Make new address or addresses
