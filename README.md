# EPC069-12_for_Billomat

This is a bunch of simple PHP scripts with a GUI that let you send your Billomat emails with an attached QR code. The code contains payment information that comply with the european standard EPC069-12 (in Germany sometimes called GiroCode). If read by a mobile banking app the payer has all payment information and just needs to confirm the money transfer.

## Warnings 

* Use at your own risk with live data, after thoroughly testing with test data
* You need to know PHP and your server, as you will deal with your sensitive invoice information, that needs to be protected.
* If you are not using Billomat, just ask your invoice software provider to implement EPC069-12. It's easy.

## Usage

* The script shows all invoices from Billomat that fulfill these requirement: 
 * Status "outstanding" (invoice must be completed)
 * Payment method "Transfer"
 * Currency "EUR"
* There are 3 actions: Show QR code, Send single invoices, Send all selected invoices
* You can enter or change your client's email addresses in this tool, but it is recommended to have it in Billomat
* It's not a must to create / show the QR code before sending an invoice, it's just for checking purposes. Read the code with a scanner or banking app. The results should look similar to this:
 
        BCD                     - always BCD
        001                     - always 001
        2                       - encoding 
        SCT                     - always SCT
        BHBLDEHHXXX             - your BIC  
        Marvin Ludwig           - your name
        DE71110220330123456789  - your IBAN
        EUR6.9                  - invoice amount (always EUR)
        OTHR                    - always OTHR
        RE4  KD1 Testkunde 1    - payment reference (default: invoice #, invoice label, client #, client name)
* After sending an invoice, the invoice will not be shown in the list after the next page reload

## Installation

* Make a folder on your web server that is only accesible by you, e. g. protected by HTTP Auth.
* Save all files into that folder.
* For maximum security move the config.php to a place that is not accesible from the web an adjust the path in billomat_epc06912.php on line 6 and index.php on line 14.
* Fill in USER_ID and API_KEY into config.php. Your USER_ID is the subdomain of your Billomat portal, like https://USER_ID.billomat.net. The API_KEY is a per user key, you can get it in Billomat in Settings > 
Administration > Employees > click on employee > activate API access > Display API key
* You're ready to go. You might check the other options in config.php but for a first test run, you should be fine.

## A note on rate limits

Billomat has a rate limit of 300 API calls per 15 minutes. It's summed up for all your applications that call the API. For this tool we need 1 to 3 calls per invoice (one to get the invoice, one to get the client, one to send the invoice). If you have a lot of invoices and / or some other applications that use the API, you should consider raising the limit to 1000 calls. Just go to Settings > Administration > Apps, register the app and enter APP_ID and APP_SECRET into the config.php file.

## Included third-party libraries:
PHP QR Code under LGPL 3, Copyright (C) Dominik Dzienia, http://phpqrcode.sourceforge.net/  
Font Awesome under SIL OFL 1.1,  Copyright (C) Dave Gandy, http://fortawesome.github.io/Font-Awesome/
