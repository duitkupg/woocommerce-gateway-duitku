import React, { useState, useEffect } from 'react';
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { useDispatch} from '@wordpress/data';
import { decodeEntities } from '@wordpress/html-entities';
import { getSetting } from '@woocommerce/settings';
import { PAYMENT_STORE_KEY } from '@woocommerce/block-data'; // Import data store key for WooCommerce Blocks payment store
import { extensionCartUpdate } from '@woocommerce/blocks-checkout'; // Import function to trigger cart updates in WooCommerce Blocks
import { subscribe, select } from '@wordpress/data'; // Import functions to subscribe to state changes and select data from the store
import { Spinner } from '@wordpress/components'; // Commponents For Loading

let previouslyChosenPaymentMethod = null;

// Function to retrieve the path for the payment method logo
const getLogoPath = (methodName) => {
	try {
		return require(`../../../includes/assets/${methodName}.png`);
	} catch (e) {
		console.error('Logo not found:', methodName);
		return ''; 
	}
};

// Subscribe to changes in the WooCommerce Blocks payment store
subscribe(function () {
	// Get the currently active payment method
	const chosenPaymentMethod = select(PAYMENT_STORE_KEY).getActivePaymentMethod();
	console.log('previously payment method: ', previouslyChosenPaymentMethod);

	// If the payment method has changed
	if (chosenPaymentMethod !== previouslyChosenPaymentMethod) {
		previouslyChosenPaymentMethod = chosenPaymentMethod;
		console.log('chosen payment method in if function', chosenPaymentMethod);
		// Update cart data with the namespace and the chosen payment method
			extensionCartUpdate({
				namespace: 'duitku_va',
				data: { method: chosenPaymentMethod },
			});
	}
	console.log('after chosen payment method: ', chosenPaymentMethod);
}, PAYMENT_STORE_KEY);

// Function to register Duitku payment method with WooCommerce Blocks
const registerDuitkuPaymentMethod = (methodName, defaultLabel) => {
	// Retrieve the payment method settings from WooCommerce (default to an empty object if settings are not available)
	const settings = getSetting(`${methodName}_data`, {});
	console.log(`${methodName} settings:`, settings);
	const logoPath = getLogoPath(methodName);

	/**
	 * Content component for the payment method description.
	 * This renders the description content for the "Duitku V2" payment method.
	 */

	const Content = () => {
		const [isLoading, setIsLoading] = useState(false);
		// Dispatch functions to signal that the checkout is calculating
		const { __internalIncrementCalculating, __internalDecrementCalculating } = useDispatch('wc/store/checkout');

		// Effect to simulate loading behavior
		useEffect(() => {
			const fetchData = async () => {
				setIsLoading(true);
				__internalIncrementCalculating();

				await new Promise((resolve) => setTimeout(resolve, 3000));

				setIsLoading(false);
				__internalDecrementCalculating();
			};
			fetchData();
		}, []);

		return isLoading ? (
			<Spinner />
		) : (
			decodeEntities(settings.description || '')
		);
	};

	const Label = ({ components }) => {
		const { PaymentMethodLabel } = components;
		const label = decodeEntities(settings.title) || defaultLabel;
		return (
			<>
				<PaymentMethodLabel text={label} />
				<img src={logoPath} style={{ marginLeft: '3px' }} />
			</>
		);
	};

	/**
	 * Duitku V2 payment method config object to register.
	 * This object contains the properties and methods needed to register the Duitku V2 payment method with WooCommerce Blocks.
	 */
	const paymentMethod = {
		name: methodName,
		label: <Label />,
		content: <Content />,
		edit: <Content />,
		canMakePayment: () => true,
		paymentMethodId: methodName,
		ariaLabel: decodeEntities(settings.title) || defaultLabel,
		supports: {
			features: settings.supports,
		},
	};

	// Log and register the payment method
	console.log(`Registering ${methodName} payment method:`, paymentMethod);
	registerPaymentMethod(paymentMethod);
};

// Register all supported Duitku payment methods
registerDuitkuPaymentMethod('duitku_va_permata', ('Duitku VA Permata', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_va_bni', ('Duitku VA BNI', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_ovo', ('Duitku OVO', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_credit_card', ('Duitku Credit Card', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_bca', ('Duitku BCA Klikpay', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_va_atm_bersama', ('Duitku VA ATM Bersama', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_va_mandiri_h2h', ('Duitku VA Mandiri Direct', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_va_cimb_niaga', ('Duitku VA CIMB Niaga', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_va_maybank', ('Duitku VA Maybank', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_va_ritel', ('Duitku VA Ritel', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_shopee', ('Duitku QRIS by ShopeePay', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_indodana', ('Duitku INDODANA', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_shopeepay_applink', ('Duitku ShopeePay Applink', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_linkaja_applink', ('Duitku LinkAja Applink', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_linkaja_qris', ('Duitku LinkAja QRIS', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_nobu_qris', ('Duitku QRIS by NOBU', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_va_bca', ('Duitku VA BCA', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_credit_card_migs', ('Duitku Credit Card MIGS', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_dana', ('Duitku DANA', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_indomaret', ('Duitku Indomaret', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_pospay', ('Duitku PosPay', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_briva', ('Duitku BRIVA', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_bnc', ('Duitku BNC/Bank Neo Commerce', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_atome', ('Duitku ATOME', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_jenius_pay', ('Duitku JENIUS PAY', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_gudang_voucher_qris', ('Duitku Gudang Voucher QRIS', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_va_sampoerna', ('Duitku VA Sampoerna', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_va_artha', ('Duitku VA Artha Graha', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_va_danamon_h2h', ('Duitku VA Danamon', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_va_bsi', ('Duitku VA BSI', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_nusapay_qris', ('Duitku Nusapay QRIS', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_tokopedia_card_payment', ('Duitku Tokopedia Card Payment', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_tokopedia_e_wallet', ('Duitku Tokopedia E-Wallet', 'wc-duitku'));
registerDuitkuPaymentMethod('duitku_tokopedia_others', ('Duitku Tokopedia Others', 'wc-duitku'));
