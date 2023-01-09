/**
 * @brief multiToc, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Tomtom, Kozlika, Franck Paul, Pierre Van Glabeke and contributors
 *
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
jsToolBar.prototype.elements.multiToc = {
	type: 'button',
	title: 'Table of content',
	icon: 'index.php?pf=multiToc/img/bt_multitoc.png',
	fn: {
		wiki: function() { this.encloseSelection("\n\n::TOC::\n\n",''); },
		xhtml: function() { this.encloseSelection("\n<p>::TOC::</p>\n",''); },
		wysiwyg: function() {
			var c = this.applyHtmlFilters(this.ibody.innerHTML);
			var s = '<p>::TOC::</p>';
			this.ibody.innerHTML = c + s;
		}
	}
};