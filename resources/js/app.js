import '../css/app.css';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';

document.addEventListener('alpine:init', () => {
    Alpine.data('tiptapEditor', (initialContent = '', wireProperty = 'contenidoEditado') => {
        // Editor stored outside Alpine's reactive proxy to avoid __v_isRef conflicts
        let editor = null;

        return {
            init() {
                const wire = this.$wire;
                this.$nextTick(() => {
                    editor = new Editor({
                        element: this.$refs.content,
                        extensions: [StarterKit, Underline],
                        content: initialContent,
                        editorProps: {
                            attributes: {
                                class: 'tiptap-editor focus:outline-none',
                            },
                        },
                        onUpdate({ editor: e }) {
                            wire.set(wireProperty, e.getHTML());
                        },
                    });
                });
            },

            destroy() {
                editor?.destroy();
                editor = null;
            },

            cmd(action) {
                if (!editor) return;
                const c = editor.chain().focus();
                const map = {
                    bold:         () => c.toggleBold().run(),
                    italic:       () => c.toggleItalic().run(),
                    underline:    () => c.toggleUnderline().run(),
                    h1:           () => c.toggleHeading({ level: 1 }).run(),
                    h2:           () => c.toggleHeading({ level: 2 }).run(),
                    h3:           () => c.toggleHeading({ level: 3 }).run(),
                    bulletList:   () => c.toggleBulletList().run(),
                    orderedList:  () => c.toggleOrderedList().run(),
                    clear:        () => c.clearNodes().unsetAllMarks().run(),
                };
                map[action]?.();
            },

            active(type, attrs = {}) {
                return editor?.isActive(type, attrs) ?? false;
            },
        };
    });
});
