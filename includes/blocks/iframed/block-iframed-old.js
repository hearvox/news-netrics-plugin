/**
 * Iframe Gutenberg Block
 */
var __                = wp.i18n.__;
var createElement     = wp.element.createElement;
var registerBlockType = wp.blocks.registerBlockType;
var RichText          = wp.editor.RichText;
var InspectorControls = wp.editor.InspectorControls;
var TextControl       = wp.components.TextControl;

function iframedBlock( attributes ) {
    // If width blank, default to 100%.
    var width = (attributes.width) ? parseInt(attributes.width) + 'px' : '100vw' ;

    // HTML iframe with user-entered vars for src, style, width, and height attributes.
    return '<iframe src="' + attributes.url +'" style="width: ' + width + '; height: ' + parseInt(attributes.height) + 'px;" class="nn-block-iframed" frameBorder="0"></iframe>';
}

/**
 * Register block
 */
registerBlockType('news-netrics/iframed',{

    title: __( 'Iframed', 'news-netrics' ),
    icon: 'tide',
    category: 'widgets',

    supports: { // Use alignment toolbar.
        align: true,
        anchor: true,
    },

    attributes: { // HTML iframe attributes
        content: {
            type: 'string',
            source: 'html',
            selector: 'div',
        },
        url: {
            type: 'string',
            default: '',
        },
        width: {
            type: 'string',
        },
        height: {
            type: 'string',
            default: '360',
        },
    },

    edit: function( props ) {
        var attributes = props.attributes;
        var content    = iframedBlock( attributes );

        const controls = [
            createElement(
                InspectorControls,
                {},
                createElement(
                    TextControl,
                    {
                        onChange: (url) => {
                            props.setAttributes( {url} )
                        },
                        label: __( 'URL', 'news-netrics' ),
                        value: props.attributes.url
                    }
                ),
                createElement(
                    TextControl,
                    {
                        onChange: (width) => {
                            props.setAttributes( {width} )
                        },
                        label: __( 'Width (pixels or blank for 100%)', 'news-netrics' ),
                        value: props.attributes.width
                    }
                ),
                createElement(
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

        return [controls,
                    createElement(
                        RichText.Content, {
                            value: content
                        }
                    ),
               ];
    },

    save: function( props ) {

        var attributes = props.attributes;
        var content = iframedBlock( attributes );

        return createElement( RichText.Content, {
            value: content
        } );
    },

});
