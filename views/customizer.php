<div class="icss-fullscreen-wrapper">
    <aside class="icss-sidebar">
        <div class="icss-editor">
            <div class="icss-error-block">
                <code class="icss-error-container"></code>
            </div>
            <div id="monaco-editor"></div>
        </div>

        <div class="icss-options-bar">
            <div class="icss-options-wrapper">
                <button class="customizer-save-button button button-primary button-large">Save</button>

                <a class="icss-open-options" href="#">
                    <span class="dashicons dashicons-admin-generic"></span>
                </a>
                <div class="icss-options-menu">
                    <div class="icss-theme-options">
                        <label for="selectTheme">Color scheme:</label>
                        <select name="selectTheme" id="selectTheme">
                            <option value="vs">VS Light</option>
                            <option value="vs-dark">VS Dark</option>
                        </select>
                    </div>

                    <div class="icss-theme-options">
                        <label for="selectPP">CSS preprocessor</label>
                        <select name="selectPP" id="selectPP">
                            <option value="css">CSS</option>
                            <option value="scss">SCSS (Sass)</option>
                        </select>
                    </div>

                    <div class="icss-theme-options">
                        <label for="selectMinify">Minify</label>
                        <select name="selectMinify" id="selectMinify">
                            <option value="on">On</option>
                            <option value="off">Off</option>
                        </select>
                    </div>

                    <div class="icss-back">
                        <a href="<?= get_dashboard_url() ?>">
                            Back to admin dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    <iframe class="icss-frame" src="<?= get_home_url(null, '/') ?>"></iframe>
</div>