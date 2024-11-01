=== ShippinGo Ecommerce Delivery - ShippinGo ===
Contributors: Shippingo1
Tags: chita,hfd,tapuz,ydm,
Requires at least: 5.7.0
Tested up to: 6.6.1
Stable tag: 1.0.16
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Languages: en_US, he_IL

== ShippinGo Ecommerce Delivery - Seamlessly Sync Your WooCommerce Orders with All Delivery Services ==

ShippinGo Ecommerce Delivery is your go-to solution for automating the shipping process of your WooCommerce store. This plugin allows you to sync your orders with a wide range of delivery companies, streamlining your shipping operations.

= Plugin Features =
1. **Standard Delivery (Door to Door)**: Easily manage regular deliveries straight to your customers' doors.
2. **Reverse Delivery**: Simplify the process of returning items from customers back to your location.
3. **Govina Delivery**: Handle complex shipping scenarios involving multiple destinations, including receiving payments from customers.
4. **Double Shipping**: Manage complex shipping scenarios involving multiple destinations.
5. **Bulk Shipment Creation**: Efficiently create multiple shipments at once.
6. **Pickup Point Collection - Map Selection**: Allow customers to choose pickup points via an interactive map (Google Maps supported).
7. **Pickup Point Collection - List Selection**: Offer a list-based selection of pickup points for customers.
8. **Shipping Label Printing**: Generate and print shipping labels with just a few clicks.
9. **Bulk Shipping Label Printing**: Bulk generate and print shipping labels with just a few clicks.
10. **Delivery Status Updates**: Receive real-time updates on the status of deliveries.
11. **Delivery Cancellation**: Cancel shipments directly from your WooCommerce dashboard.

= Setting Up an Account =
Need help with installation? Check out our [Plugin Installation Guide](https://www.shippingo.ai/installation).

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/shippingo` directory, or install the plugin directly from the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure the plugin settings via the ShippinGo setting page.

== Description ==

ShippinGo Ecommerce Delivery enables seamless syncing of your WooCommerce orders with a variety of delivery companies, automating the entire shipping process.

= Supported Shipping Companies =
- All companies who are using Baldar
- All companies who are using Run
- Chita
- HFD
- Negev
- Tapuz
- Tamnun
- SPEEDWAY
- KATZ
- YDM
- Rimon
- KEXPRESS
- Kal Kanesher
- Shipping
- TS delivery
- ISGAV
- Davar Rishon
- YDM
- CARGO
- Sosna
- Buzzer
- ZigZag
- Sdeliveries

Didn't see your courier service listed? No problem! Contact us, and we'll add your preferred shipping company.

== External Services ==

This plugin integrates with external services to provide its functionality. Below are the details of the third-party services used by the plugin:

### 1. Shippingo Platform API
**Service Description**:  
This plugin connects to the Shippingo Platform API to manage shipping processes, including order registration and label generation.

**Data Sent**:  
- **Account creation**: There is a registraion form to create your account in Shippingo.
- **Order Data**: Each time an order is added or updated, the corresponding order data is sent.

**Conditions for Data Transmission**:  
- Data is sent when the user interacts with the plugin to register an order or generate a shipping label.

**Terms of Service and Privacy Policy**:  
- [Shippingo Terms of Service](https://shippingo.ai/terms)
- [Shippingo Privacy Policy](https://shippingo.ai/privacy)

### 2. Shipping Label Generation
**Service Description**:  
This service is used to generate shipping labels for orders processed through the Shippingo platform.

**Data Sent**:  
- **Label Request**: When a shipment is created, the plugin sends the order ID and any other necessary data to the Shippingo platform to generate a shipping label.

**Conditions for Data Transmission**:  
- Data is transmitted each time a shipping label is requested.

**Terms of Service and Privacy Policy**:  
- [Shippingo Terms of Service](https://shippingo.ai/terms)
- [Shippingo Privacy Policy](https://shippingo.ai/privacy)

### Additional Information
- All requests to the Shippingo Platform API are made using secure HTTPS protocols to ensure data protection.


== Frequently Asked Questions ==

For more information, please visit our FAQ section.

== Screenshots ==

1. **Plugin Settings Screen** - Configure ShippinGo easily from the plugin settings page.
2. **Order Sync in Action** - Automatically sync your orders with multiple delivery services.
3. **Bulk Shipment Creation** - Create bulk shipments in just a few clicks.
4. **Map Selection for Pickup Points** - Let customers choose their preferred pickup location using an interactive map.
5. **Shipping Label Printing** - Generate and print shipping labels directly from the WooCommerce dashboard.

== Changelog ==

= 1.0.12 =
* Initial release of ShippinGo
