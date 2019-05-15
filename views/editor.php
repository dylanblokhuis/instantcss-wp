<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access
?>

<div class="icss-body">

    <div class="icss-title-wrapper">
        <h1 class="icss-title">Instant CSS</h1>

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
    </div>

    <div class="icss-wrapper">
        <div class="icss-editor">
            <div class="icss-error-block">
                <code class="icss-error-container"></code>
            </div>
            <div id="monaco-editor"></div>
        </div>
        <div class="icss-options">
            <button class="save-button button button-primary button-large">Save</button>
        </div>
    </div>

</div>