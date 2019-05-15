// set webpack public path for monaco workers
__webpack_public_path__ = wordpress.plugins_url + 'assets/dist/';
import * as monaco from 'monaco-editor/esm/vs/editor/editor.api.js';

import postcss from 'postcss';
import sass from 'sass.js';
import autoprefixer from 'autoprefixer';
import csso from 'postcss-csso';

class Editor {
    constructor() {
        this.vars();
        this.init();
    }

    vars() {
        this.assetPath = wordpress.plugins_url;
        this.ajaxUrl = wordpress.ajax_url;
        window.error = null;
    }

    init() {
        this.editor();
        this.loadCSS();
        this.loadTheme();
        this.loadPreprocessor();
        this.loadMinify();
        this.jQueryEditor();
        if (wordpress.is_customizer === 'active') {
            jQuery('iframe').load(() => {
                this.customizer();
            })
        }
    }

    editor() {
        this.monacoEditor = monaco.editor.create(document.getElementById('monaco-editor'), {
            language: 'css'
        });

        this.monacoEditor.onKeyUp(e => {
            if (window.error) {
                Editor.setError();
            }
            if (window.isSaved == true) {
                jQuery('.save-button').text('Save');
                window.isSaved = false;
            }
        });
    }

    compileCSS() {
        let autoprefixerOptions = { browsers: ['last 2 version', 'ie >= 9', 'iOS >= 7', 'android >= 4.1'] };

        let unsavedCSS = this.monacoEditor.getValue();
        if (this.preprocessor !== 'scss') {
            if (this.ifMinify == 'on') {
                postcss([ autoprefixer(autoprefixerOptions), csso() ]).process(unsavedCSS)
                    .then(result => {
                        this.saveCSS(unsavedCSS, result.css);
                    })
                    .catch(error => {
                        Editor.setError(error.toString());
                    });
            } else {
                postcss([ autoprefixer() ]).process(unsavedCSS)
                    .then(result => {
                        this.saveCSS(unsavedCSS, result.css);
                    })
                    .catch(error => {
                        Editor.setError(error.toString());
                    });
            }
        } else {
            sass.compile(unsavedCSS, result => {
                if (result.status) {
                    Editor.setError(result.message, result.line);
                } else {
                    if (this.ifMinify == 'on') {
                        console.log('Minify is on, minifying css');
                        postcss([ autoprefixer(), csso() ]).process(result.text)
                            .then(result => {
                                this.saveCSS(unsavedCSS, result.css);
                            });
                    } else {
                        console.log('Minify is off, not minifying css');

                        postcss([ autoprefixer() ]).process(result.text)
                            .then(result => {
                                this.saveCSS(unsavedCSS, result.css);
                            });
                    }
                }
            })
        }
    }

    loadCSS() {
        let data = {
            'action' : 'icss_get_css',
        };

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                this.monacoEditor.setValue(response);
            },
        });
    }

    saveCSS(rawCSS, compiledCSS) {
        Editor.setError();

        if (wordpress.is_customizer === 'active') {
            this.setLivePreviewCSS(compiledCSS)
        }

        let data = {
            'action' : 'icss_save_css',
            'css' : rawCSS,
            'postcss' : compiledCSS
        };

        jQuery.ajax({
            url : this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                console.log(response);
                jQuery('.save-button').removeClass('is-busy').text('Saved!');
                jQuery('.customizer-save-button').removeClass('is-busy').text('Saved!');
                window.isSaved = true;
            }
        })
    }

    loadTheme() {
        let data = {
            'action' : 'icss_get_theme',
        };

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                monaco.editor.setTheme(response);
                jQuery("#selectTheme").val(response);
            },
        });

    }

    changeTheme(theme) {
        monaco.editor.setTheme(theme);

        let data = {
            'action' : 'icss_save_theme',
            'theme' : theme
        };

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                console.log(response);
            },
        });
    }

    changePreprocessor(preprocessor) {
        monaco.editor.setModelLanguage(this.monacoEditor.getModel(), preprocessor);

        console.log('Language changed to', preprocessor);

        let data = {
            'action' : 'icss_save_preprocessor',
            'preprocessor' : preprocessor
        };

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                this.preprocessor = preprocessor;
                console.log(response);
            },
        });
    }

    loadPreprocessor() {
        let data = {
            'action' : 'icss_get_preprocessor',
        };

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                this.preprocessor = response;
                monaco.editor.setModelLanguage(this.monacoEditor.getModel(), response);
                jQuery("#selectPP").val(response);
            },
        });
    }

    changeMinify(value) {
        this.ifMinify = value;

        let data = {
            'action' : 'icss_save_minify',
            'minify' : value
        };

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                console.log(response);
            },
        });
    }

    loadMinify() {
        let data = {
            'action' : 'icss_get_minify',
        };

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                console.log(response);
                this.ifMinify = response;
                jQuery("#selectMinify").val(response);
            },
        });
    }

    static setError(error, line) {
        window.error = error;
        if (error) {
            if (line) {
                error = "Line " + line + ': ' + error
            }
            jQuery('.icss-error-block').fadeIn();
            jQuery('.save-button').text('Oops!').addClass('button-danger').removeClass('is-busy');
            jQuery('.customizer-save-button').text('Oops!').addClass('button-danger').removeClass('is-busy');
            jQuery('.icss-error-container').text(error);
        } else {
            window.error = null;
            jQuery('.icss-error-block').fadeOut();
            jQuery('.save-button').text('Save').removeClass('button-danger').removeClass('is-busy');
            jQuery('.customizer-save-button').text('Save').removeClass('button-danger').removeClass('is-busy');
        }

    }

    // jQuery actions
    jQueryEditor() {
        let self = this;

        /* When user presses CTRL + S */
        jQuery(window).bind('keydown', function(event) {
            if (event.ctrlKey || event.metaKey) {
                switch (String.fromCharCode(event.which).toLowerCase()) {
                case 's':
                    event.preventDefault();
                    jQuery('.save-button').addClass('is-busy').text('Saving..');
                    jQuery('.customizer-save-button').addClass('is-busy').text('Saving..');
                    self.compileCSS()
                    break;
                }
            }
        });
        jQuery('.icss-open-options').click(function () {
            jQuery('.icss-options-menu').slideToggle()
        })
        jQuery('.save-button').click(function () {
            jQuery('.save-button').addClass('is-busy').text('Saving..');
            self.compileCSS();
        });
        jQuery('.customizer-save-button').click(function () {
            jQuery('.customizer-save-button').addClass('is-busy').text('Saving..');
            self.compileCSS();
        });
        jQuery('#selectTheme').change(function () {
            self.changeTheme(jQuery(this).val());
        });
        jQuery('#selectPP').change(function () {
            self.changePreprocessor(jQuery(this).val());
        });
        jQuery("#selectMinify").change(function () {
            self.changeMinify(jQuery(this).val());
        })
    }

    customizer() {
        let head = jQuery('.icss-frame').contents().find('head')
        head.append('<style>#wpadminbar { display: none; } html { margin-top: unset !important; }</style>')
        head.append('<style id="icss-live-preview"></style>')
        jQuery('.icss-frame').css('opacity', 1)
    }

    setLivePreviewCSS(compiledCSS) {
        let head = jQuery('.icss-frame').contents().find('head')
        head.find('#icss-live-preview').text(compiledCSS)
        head.find('#icss-custom-styles-css').remove()
    }
}

new Editor();