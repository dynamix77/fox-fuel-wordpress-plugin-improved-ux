/**
 * Fox Fuel Selector Gutenberg Block
 */

(function() {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.editor;
    const { PanelBody, SelectControl, ToggleControl } = wp.components;
    const { createElement: el } = wp.element;

    registerBlockType('fox-fuel/selector', {
        title: 'Fox Fuel Price Protection Plan Selector',
        description: 'Display the Fox Fuel heating oil price protection plan selector',
        icon: 'money-alt',
        category: 'widgets',
        
        attributes: {
            mode: {
                type: 'string',
                default: 'both'
            },
            theme: {
                type: 'string',
                default: 'light'
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { mode, theme } = attributes;

            return [
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Fox Fuel Selector Settings' },
                        el(SelectControl, {
                            label: 'Display Mode',
                            value: mode,
                            options: [
                                { label: 'Both (Mode Selector)', value: 'both' },
                                { label: 'Guided Form Only', value: 'guided' },
                                { label: 'Chat Only', value: 'chat' }
                            ],
                            onChange: function(value) {
                                setAttributes({ mode: value });
                            }
                        }),
                        el(SelectControl, {
                            label: 'Theme',
                            value: theme,
                            options: [
                                { label: 'Light', value: 'light' },
                                { label: 'Dark', value: 'dark' }
                            ],
                            onChange: function(value) {
                                setAttributes({ theme: value });
                            }
                        })
                    )
                ),
                el('div', {
                    className: 'fox-fuel-block-preview',
                    style: {
                        padding: '20px',
                        border: '2px dashed #ccc',
                        borderRadius: '8px',
                        textAlign: 'center',
                        backgroundColor: '#f9f9f9'
                    }
                },
                    el('div', {
                        style: {
                            fontSize: '48px',
                            marginBottom: '10px'
                        }
                    }, '🦊'),
                    el('h3', {
                        style: {
                            color: '#FF6B35',
                            marginBottom: '10px'
                        }
                    }, 'Fox Fuel Price Protection Plan Selector'),
                    el('p', {
                        style: { color: '#666' }
                    }, 'Mode: ' + mode.charAt(0).toUpperCase() + mode.slice(1) + ' | Theme: ' + theme.charAt(0).toUpperCase() + theme.slice(1)),
                    el('p', {
                        style: { 
                            fontSize: '14px',
                            color: '#888',
                            fontStyle: 'italic'
                        }
                    }, 'Preview unavailable in editor - view on frontend to see the full selector')
                )
            ];
        },

        save: function(props) {
            // Return null because we're using PHP render callback
            return null;
        }
    });
})();
