(function() 
{
	tinymce.PluginManager.requireLangPack('wpsociallikes');

	tinymce.create('tinymce.plugins.WpsociallikesPlugin', 
		{
			init : function(ed, url) 
			{
				ed.addCommand('mceSocial', 
					function() 
					{						
						var newcontent = '<ul class="social-likes"><li class="vkontakte" title="Поделиться ссылкой во Вконтакте">Вконтакте</li><li class="facebook" title="Поделиться ссылкой на Фейсбуке">Facebook</li><li class="twitter" title="Поделиться ссылкой в Твиттере">Twitter</li><li class="plusone" title="Поделиться ссылкой в Гугл-плюсе">Google+</li></ul>';
						tinyMCE.activeEditor.selection.setContent(newcontent);	
					}
				);
				
				ed.addButton('social', 
					{
						title : 'Social Likes',
						cmd : 'mceSocial',
						image : url + '/img/like.png'
					}
				);
			},

			createControl : function(n, cm) 
			{
				return null;
			},

			getInfo : function() 
			{
				return {
					longname : 'WP Social Likes plugin',
					version : "0.1"
				};
			}
		});

	tinymce.PluginManager.add('wpsociallikes', tinymce.plugins.WpsociallikesPlugin);
})();
