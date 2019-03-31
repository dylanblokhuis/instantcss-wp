// set webpack public path for monaco workers
__webpack_public_path__ = wordpress.plugins_url + 'assets/dist/';
import * as monaco from 'monaco-editor/esm/vs/editor/editor.api.js';

import postcss from 'postcss';
import sass from 'sass.js';
import autoprefixer from 'autoprefixer';
import minify from './minify';

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
        let unsavedCSS = this.monacoEditor.getValue();
        if (this.preprocessor !== 'scss') {
            if (this.ifMinify == 'on') {
                console.log('Minify is on, minifying css');
                postcss([ autoprefixer(), minify ]).process(unsavedCSS)
                    .then(result => {
                        this.saveCSS(unsavedCSS, result.css);
                    })
                    .catch(error => {
                        Editor.setError(error.toString());
                    });
            } else {
                console.log('Minify is off, not minifying css');

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
                        postcss([ autoprefixer(), minify ]).process(result.text)
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
        jQuery('.save-button').addClass('is-busy').text('Saving..');
        Editor.setError();
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
            jQuery('.save-button').text('Oops!').addClass('button-danger');
            jQuery('.icss-error-container').text(error);
        } else {
            window.error = null;
            jQuery('.icss-error-block').fadeOut();
            jQuery('.save-button').text('Save').removeClass('button-danger');
        }

    }





    // jQuery actions
    jQueryEditor() {
        let self = this;

        jQuery('.save-button').click(function () {
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


}

new Editor();