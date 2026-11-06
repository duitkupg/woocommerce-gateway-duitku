(function () {
    const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
    const { getSetting } = window.wc.wcSettings;
    const { createElement } = window.wp.element;
    const { decodeEntities } = window.wp.htmlEntities;

    const NAME = 'duitku_shopeepay_applink';
    // key is always "<name>_data"
    const settings = getSetting(NAME + '_data', {});

    const isMobile =
        /android|iphone|ipad|ipod|mobile|windows phone|blackberry|opera mini/i.test(
            navigator.userAgent
        );

    const label = decodeEntities(settings.title || 'ShopeePay Applink');

    const Label = () =>
        createElement(
            'span',
            { style: { display: 'flex', alignItems: 'center', justifyContent: 'space-between', width: '100%' } },
            label,
            settings.icon
                ? createElement('img', { src: settings.icon, alt: 'Pembayaran Duitku', style: { height: '24px' } })
                : null
        );

    const Content = () => createElement('div', null, decodeEntities(settings.description || ''));

    registerPaymentMethod({
        name: NAME,
        label: createElement(Label),
        content: createElement(Content),
        edit: createElement(Content),
        ariaLabel: label,
        // Hidden on desktop, shown on mobile
        canMakePayment: () => isMobile,
        supports: { features: settings.supports || ['products'] },
    });
})();