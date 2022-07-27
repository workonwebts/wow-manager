// JavaScript Document
jQuery(document).ready(function($) {
/*
    tinymce.create('tinymce.plugins.WoW_book_plugin', {
        init : function(ed, url) {
                // Register command for when button is clicked
                ed.addCommand('WoW_book_insert_shortcode', function() {
                    selected = tinyMCE.activeEditor.selection.getContent();

                    if( selected ){
                        //If text is selected when button is clicked
                        //Wrap shortcode around it.
                        content =  '[shortcode]'+selected+'[/shortcode]';
                    }else{
                        content =  '[shortcode]';
                    }

                    tinymce.execCommand('mceInsertContent', false, content);
                });

            // Register buttons - trigger above command when clicked
            ed.addButton('WoW_book_button', {title : 'Insert Book shortcode', cmd : 'WoW_book_insert_shortcode', image: url + '/../images/book.png' });
        },   
    });
    // Register our TinyMCE plugin
    // first parameter is the button ID1
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('WoW_book_button', tinymce.plugins.WoW_book_plugin);
*/

   tinymce.PluginManager.add('WoW_book_button', function( editor, url ) {
        editor.addButton( 'WoW_book_button', {
            text: tinyMCE_object.button_name,
            icon: false,
			image: url + '/../images/book.png',
            onclick: function() {
                editor.windowManager.open( {
                    title: tinyMCE_object.button_title,
                    body: [
                        {
                            type   : 'listbox',
                            name   : 'shortlist',
                            label  : 'Campo',
                            values : [
                                { text: 'Titolo', value: 'book_title' },
                                { text: 'Descrizione', value: 'book_description' },
                                { text: 'Autore', value: 'book_autore' },
                                { text: 'Prezzo', value: 'book_price' },
                                { text: 'Prezzo Scontato', value: 'book_sale' },
                                { text: 'Reparto', value: 'book_reparto' },
                                { text: 'Categoria', value: 'book_category' },
                                { text: 'Genere', value: 'book_genere' },
                                { text: 'Editore', value: 'book_editore' },
                                { text: 'Collana', value: 'book_collana' },
                                { text: 'Codice', value: 'book_code' },
                                { text: 'Ist.Scolastico.', value: 'book_specials' }
                            ],
                            value : 'book_title' // Sets the default
                        },
                        {
                            type   : 'listbox',
                            name   : 'search_by',
                            label  : 'Ricerca Tramite',
                            values : [
                                { text: 'Codice Prodotto (ISBN)', value: 'codprod' },
                                { text: 'ID Prodotto', value: 'idprod' },
                                { text: 'Slug - Titolo Normalizzato', value: 'slug' }
                            ],
                            value : 'codprod' // Sets the default
                        },
                        {
                            type   : 'textbox',
                            name   : 'prod_id',
                            label  : 'Valore da Cercare',
                            tooltip: 'Some nice tooltip to use',
                            value  : ''
                        },
                        {
                            type   : 'checkbox',
                            name   : 'link',
                            label  : 'Aggiungi Link alla Pagina',
                            checked : false
                        }
                    ],
                    onsubmit: function( e ) {
						selected = editor.selection.getContent();
						shortcode = '[' + e.data.shortlist + ' search_by="' + e.data.search_by + '" prod_id="' + e.data.prod_id + '" link="' + (e.data.link?'y':'n') + '"]';
						if( selected ){
							//If text is selected when button is clicked Wrap shortcode around it.
							content =  shortcode + selected + '[/'+e.data.shortlist+']';
						}else{
							content =  shortcode;
						}
                        editor.insertContent( content );
                    }
                });
            },
        });
    });


});