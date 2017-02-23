(function() {
	tinymce.PluginManager.add('wcd_mce_button', function( editor, url ) {
		editor.addButton('wcd_mce_button', {
			text: 'Templates',
			icon: false,
			tooltip: 'Beaver Builder Templates',
			onclick: function() {
				editor.windowManager.open( {
					title: 'Insert Template',
					width: 400,
					height: 100,
					body: [
						{
							type: 'listbox',
							name: 'listboxName',
							label: 'Templates',
							'values': editor.settings.cptPostsList
						}
					],
					onsubmit: function( e ) {
						editor.insertContent( '[fl_builder_insert_layout id="' + e.data.listboxName + '" type="fl-builder-template"]');
					}
				});
			}
		});
	});
})();