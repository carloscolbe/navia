/*
 * Copies the static runtime assets (TinyMCE skins/themes/icons/models and the
 * ace editor libs) into publishable/assets, mirroring what webpack.mix.js did.
 */
import { cpSync } from 'node:fs';

const targets = [
    ['node_modules/tinymce/skins', 'publishable/assets/js/skins'],
    ['resources/assets/js/skins', 'publishable/assets/js/skins'],
    ['node_modules/tinymce/themes/silver', 'publishable/assets/js/themes/silver'],
    ['node_modules/tinymce/models/dom', 'publishable/assets/js/models/dom'],
    ['node_modules/tinymce/icons/default', 'publishable/assets/js/icons/default'],
    ['node_modules/ace-builds/src-noconflict', 'publishable/assets/js/ace/libs'],
];

for (const [src, dest] of targets) {
    cpSync(src, dest, { recursive: true });
    console.log(`Copied ${src} -> ${dest}`);
}
