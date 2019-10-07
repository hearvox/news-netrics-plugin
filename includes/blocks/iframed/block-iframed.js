/**
 * Iframe Gutenberg Block
 */
( function( blocks, editor, i18n, element, components, _ ) {
/**/
    const __                = i18n.__;
    const el                = element.createElement;
    const registerBlockType = blocks.registerBlockType;
    const RichText          = editor.RichText;
    const InspectorControls = wp.blockEditor.InspectorControls;
    const TextControl       = components.TextControl;


    blocks.registerBlockType( 'news-netrics/iframed', {
        title: i18n.__( 'Iframed', 'news-netrics' ),
        icon: 'tide',
        category: 'widgets',
        supports: { // Use alignment toolbar.
            align: true,
            anchor: true,
        },
        attributes: {
            url: {
                type: 'string',
            },
            width: {
                type: 'string',
            },
            height: {
                type: 'string',
                default: '360',
            },
            style: {
                type: 'string',
            },
        },
        edit: function( props ) {
            var attributes = props.attributes;
            // If width blank, default to 100%.
            var iframe_width  = (attributes.width) ? parseInt(attributes.width) + 'px' : '100vw' ;
            var iframe_style = { width: iframe_width, height: parseInt(attributes.height) + 'px' };

            const controls = [
                el(
                    InspectorControls,
                    {},
                    el(
                        TextControl,
                        {
                            onChange: (url) => {
                                props.setAttributes( {url} )
                            },
                            label: __( 'URL', 'news-netrics' ),
                            value: props.attributes.url
                        }
                    ),
                    el(
                        TextControl,
                        {
                            onChange: (width) => {
                                props.setAttributes( {width} )
                            },
                            label: __( 'Width (pixels or blank for full-width)', 'news-netrics' ),
                            value: props.attributes.width
                        }
                    ),
                    el(
                        TextControl,
                        {
                            onChange: (height) => {
                                props.setAttributes( {height} )
                            },
                            label: __( 'Height (pixels)', 'news-netrics' ),
                            value: props.attributes.height
                        }
                    ),
                ),
            ];

            return [controls, el(
                'iframe',
                {
                    className: props.className,
                    src: attributes.url,
                    style: iframe_style,
                    width: iframe_width,
                    height: attributes.height,
                    frameborder: 'no'
                }
            )];
        },
        save: function( props ) {
            var attributes = props.attributes;
            var iframe_width  = (attributes.width) ? parseInt(attributes.width) + 'px' : '100vw' ;
            var iframe_style = { width: iframe_width, height: parseInt(attributes.height) + 'px' };

            return el(
                'iframe',
                { className: props.className, src: attributes.url, style: iframe_style, width: iframe_width, height: attributes.height, frameborder: 'no' }
            )
        },
    } );

} )(
    window.wp.blocks,
    window.wp.editor,
    window.wp.i18n,
    window.wp.element,
    window.wp.components,
    window._,
);
