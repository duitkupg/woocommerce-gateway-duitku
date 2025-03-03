=== woocommerce gateway duitku ===
/*
PPlugin Name:  Duitku Payment Gateway
Plugin URI:   https://docs.duitku.com/#woocommerce-duitku
Description:  Duitku Payment Gateway 
Version:      2.6
Author:       Duitku Development Team
Contributors: anggiyawan@duitku.com, hanithiojuwono
Author URI:   http://duitku.com
Tags:         paymentgateway, duitku, BCA, Mandiri, BRI, CIMB, BNI
Requires at least: 4.7
Tested up to: 5.6
Stable tag: 4.3
Requires PHP: 7.0
Author URI:   http://duitku.com
License:      GPLv2 or Later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

== Description ==

Do you want the best solution to accept Credit Cards, e-wallet, and Various Bank Transfers on your website? Our Payment Gateway for WooCommerce plugin integrates with your WooCommerce store and lets you accept those payments through our payment gateway.
Securely accept major credit cards, View and manage transactions from one convenient place – your Duitku dashboard.

Supported Payment Channels :

1.	Credit Card Aggregator full-payment (Visa, Master, JCB)
2.	Credit Card Facilitator installment and full-payment (Visa, Master, JCB, AMEX)
3.	BCA KlikPay
4.	BCA Virtual Account
5.	Mandiri Virtual Account (Deprecated)
6.	Mandiri Virtual Account
7.	Permata Bank Virtual Account
8.	ATM Bersama
9.	CIMB Niaga Virtual Account
10.	BNI Virtual Account
11.	Maybank Virtual Account
12.	Retail (Alfamart,  Pegadaian and Pos Indonesia)
13.	OVO
14.	Indodana Paylater
15.	Shopee Pay
16.	Shopee Pay Apps
17.	Bank Artha Graha
18.	Bank Sahabat Sampoerna
19.	LinkAja Apps (Percentage Fee)
20.	LinkAja Apps (Fixed Fee)
21.	DANA

Supported Payment Channels :

1.	Credit Card (Visa / Master)
2.	BCA KlikPay
3.	BCA Virtual Account
4.	Mandiri Virtual Account (Deprecated)
5.	Mandiri Virtual Account
6.	Permata Bank Virtual Account
7.	ATM Bersama
8.	CIMB Niaga Virtual Account
9.	BNI Virtual Account
10.	Maybank Virtual Account
11.	Ritel
12.	OVO
13.	Indodana Paylater
14.	Shopee Pay
15.	Shopee Pay Apps
16.	Bank Artha Graha
18.	Bank Sahabat Sampoerna
19.	LinkAja Apps (Percentage Fee)
20.	LinkAja Apps (Fixed Fee)
21.	DANA

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