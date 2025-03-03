=== Duitku Payment Gateway ===

Plugin Name:  Duitku Payment Gateway
Plugin URI:   https://docs.duitku.com/#woocommerce-duitku
Description:  Duitku Payment Gateway V2 API
Version:      2.11.11
Author:       Duitku Development Team
Contributors: anggiyawan@duitku.com, hanithiojuwono, rayhanduitku
Author URI:   http://duitku.com
Tags:         paymentgateway, duitku, BCA, Mandiri, BRI, CIMB, BNI, Permata, ATM Bersama, CIMB Niaga, Maybank, Alfamart, Pegadaian, Pos Indonesia, OVO, Indodana, Shopee, Artha Graha, Sampoerna, LinkAja, DANA, Indomaret, PosPay, BRIVA, Nobu, ATOME
Requires at least: 4.7
Tested up to: 6.7.2
Stable tag: 2.11.11
Requires PHP: 7.3 or Higher
Author URI:   http://duitku.com
License:      GPLv2 or Later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html


== Description ==

Do you want the best solution to accept Credit Cards, e-wallet, and Various Bank Transfers on your website? Our Payment Gateway for WooCommerce plugin integrates with your WooCommerce store and lets you accept those payments through our payment gateway.
Securely accept major credit cards, View and manage transactions from one convenient place – your Duitku dashboard.

Supported Payment Channels :

1.	Credit Card Aggregator full-payment (Visa, Master, JCB)
2.	Credit Card Facilitator installment and full-payment (Visa, Master, JCB, AMEX)
3.	BCA KlikPay
4.	BCA Virtual Account
5.	Mandiri Virtual Account
6.	Permata Bank Virtual Account
7.	ATM Bersama
8.	CIMB Niaga Virtual Account
9.	BNI Virtual Account
10.	Maybank Virtual Account
11.	Retail (Alfamart,  Pegadaian and Pos Indonesia)
12.	OVO
13.	Indodana Paylater
14.	Shopee Pay
15.	Shopee Pay Apps
16.	Bank Artha Graha
17.	LinkAja Apps (Percentage Fee)
18.	LinkAja Apps (Fixed Fee)
19.	DANA
20.	LinkAja QRIS
21.	Indomaret
22.	PosPay
23.	BNC
24.	BRIVA
25.	QRIS by Nobu
26.	ATOME
27. Gudang Voucher QRIS
28. Jenius Pay
29. Bank Sahabat Sampoerna
30. Danamon Virtual Account
31. BSI Virtual Account

== Installation ==

Guide to installing the Duitku plugin for Woocommerce

1. Download the Duitku plugin for Woocommerce here .

2. Open your Wordpress Admin menu (generally in / wp-admin).

3. Open the Plugins menu -> Add New Page.

4. Upload the Duitku plugin file (Make sure Woocommerce is installed before adding the Duitku plugin).

5. After the plugin is installed, Duitku will appear in the list of installed plugins. Open the Plugin -> Installled Plugins page, then activate the Duitku plugin.

6. Open Woocommerce -> Settings then select the 'Duitku Global Configuration' tab.

7. Enter the Merchant Code and API Key, these parameters are created on the Duitku merchant page in the Project menu section

	Addition:

		Endpoint for the trial phase https://sandbox.duitku.com/webapi

		Endpoint for stage production https://passport.duitku.com/webapi
		
8. After the 'Duitku Global Configuration' setting is complete, open the Payment tab.

9. Select the payment channel that you will use (example: Duitku Mandiri, Duitku CIMB, Duitku Wallet, Duitku Credit Card, Duitku BCA Klikpay).

== Frequently Asked Questions ==

= What is Duitku? =

Duitku is a Payment Solution service with the best MDR (Merchant Discount Rate) fees from many Payment Channels in Indonesia. As your payment service provider, Duitku can serve payments via credit cards, bank transfers and internet banking directly to your online shop.

= How do I integrate Duitku with my website? =

Integrating online payments with Duitku is very easy, web integration using our API. (API doc: http://docs.duitku.com/docs-api.html) or using plugins for e-commerce.

== Screenshots ==

1. Checkout Page

2. Payment Page

3. Success Payment Page

4. Duitku Global Configuration Settings

== Changelog ==

= 2.11.10 June 10, 2024 =
-Woocommerce Duitku Payment Gateway API V2 Support Blocks Checkout

= 2.11.10 June 10, 2024 =
-Add new payment BSI VA
-Adjustment for order total exclude fee to include fee

= 2.11.9 Mei 2, 2024 =
-Re-add Sampoerna VA
-Add new payment Danamon VA

= 2.11.8 April 3, 2024 =
-Fix process fees in signature validation

= 2.11.7 March 8, 2024 =
-Improvement for signature validation in callback
-Fix failing status from check transaction

= 2.11.6 February 26, 2024 =
-Add new payment Jenius Pay 

= 2.11.5 July 28, 2023 =
-Add new payment Gudang Voucher QRIS  

= 2.11.4 January 05, 2022 =
-Remove Sampoerna VA 

= 2.11.3 Aug 31, 2022 =
-Add new payment ATOME 

= 2.11.2 Juni 16, 2022 =
-Change Logo Bank Neo Commerce 

= 2.11.1 April 20, 2022 =
-Improvement for input parameter phoneNumber

= 2.11 Mar 17, 2022 =
-Add BRIVA
-Add QRIS by Nobu

= 2.10 Mar 03, 2022 =
-Add Bank Neo Commerce
-Remove Deprecated Mandiri Channel

= 2.9 Sept 13, 2021 =
-Add PosPay

= 2.8 Sept 9, 2021 =
-Add Indomaret

= 2.6 Jun 6, 2021 =
-Add LinkAja QR

= 2.6 Mar 4, 2021 =
- Add LinkAja
- Add DANA
- add Sanitized & Validation Email and Phone Number feature

= 2.5 Nov 16, 2020 =

improvement 2.4 to 2.5
- Change Mandiri Virtual Account become Deprecated
- Add VA Mandiri Direct

= 2.4 Nov 06, 2020 =

improvement 2.3 to 2.4
- Change Logo Indodana

= 2.3 Oct 12, 2020 =

improvement 2.2 to 2.3
- Add BCA Virtual Account

= 2.2 June 17, 2020 = 

improvement 2.1 to 2.2:
- Add ShopeePay Applink & LinkAja Applink
- Add observer & mutation for detect device

= 2.1 Mar 12, 2020 = 

improvement 2.0 to 2.1:
- Improve Expired Period

= 2.0 Feb 06, 2020 = 

improvement 1.7 to 2.0:
- upgrade API v2
- add ShopeePay
- add Indodana
- update fitur API fee

= 1.7 Nov 01, 2019 = 

improvement 1.6 to 1.7:
- add Credit Card Fasilitator.
- add fitur fee

= 1.6 Mei 15, 2019 = 

improvement 1.5 to 1.6:
- add Mandiri Virtual Account.

= 1.5 April 22, 2017 = 

improvement 1.4 to 1.5:
- add OVO Payment.

= 1.1 April 22, 2017 = 
improvement 1.3 to 1.4:
- add ATM Bersama, BNI, CIMB Niaga and Maybank Virtual Account support.



= 1.0 =

Initial Public Release