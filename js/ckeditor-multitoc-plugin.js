/*global CKEDITOR, dotclear */
'use strict';

dotclear.ck_multitoc = dotclear.getData('ck_editor_multitoc');

{
  CKEDITOR.plugins.add('multitoc', {
    init(editor) {
      editor.addCommand('multiTocCommand', {
        exec: function(editor) {
          const s = '<p>::TOC::</p>';
          const element=CKEDITOR.dom.element.createFromHtml(s);
          editor.insertElement(element);
        },
      });

      var icon_path = this.path.replace('js','img');
      editor.ui.addButton('multiToc', {
        label: dotclear.ck_multitoc.title,
        command: 'multiTocCommand',
        icon: icon_path + 'bt_multitoc.png',
        toolbar: 'insert',
      });
    },
  });
}
