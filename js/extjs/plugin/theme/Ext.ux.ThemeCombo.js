// vim: ts=4:sw=4:nu:fdc=4:nospell
/**
  * Ext.ux.ThemeCombo - Combo pre-configured for themes selection
  * 
  * @author    Ing. Jozef Sakáloš <jsakalos@aariadne.com>
  * @copyright (c) 2008, by Ing. Jozef Sakáloš
  * @date      30. January 2008
  * @version   $Id: Ext.ux.ThemeCombo.js 668 2008-02-01 01:02:54Z jozo $
  */

Ext.ux.ThemeCombo = Ext.extend(Ext.form.ComboBox, {
    // configurables
     themeBlackText: 'Black'
    ,themeChocolateText: 'Chocolate'
    ,themeBlueText: 'Default (Blue)'
    ,themeDarkGrayText: 'Dark Gray'
    ,themeGrayText: 'Gray'
    ,themeGreenText: 'Green'
    ,themeIndigoText: 'Indigo'
    ,themeMidnightText: 'Midnight'
    ,themeOliveText: 'Olive'
    ,themePeppermintText: 'Peppermint'
    ,themePinkText: 'Pink'
    ,themePurpleText: 'Purple'
    ,themeSilverCherryText: 'SilverCherry'
    ,themeSlateText: 'Slate'
    ,themeVar:'theme'
    ,selectThemeText: 'Select Theme'
    ,lazyRender:true
    ,lazyInit:true
    ,cssPath:'./library/js/extjs/resources/css/' // mind the trailing slash

    // {{{
    ,initComponent:function() {

        Ext.apply(this, {
            store: new Ext.data.SimpleStore({
                 fields: ['themeFile', 'themeName']
                ,data: [
                     ['xtheme-default.css', this.themeBlueText]
                    ,['xtheme-gray.css', this.themeGrayText]
                    ,['xtheme-darkgray.css', this.themeDarkGrayText]
                    ,['xtheme-green.css', this.themeGreenText]
                    ,['xtheme-indigo.css', this.themeIndigoText]
                    ,['xtheme-midnight.css', this.themeMidnightText]
                    ,['xtheme-pink.css', this.themePinkText]
                    ,['xtheme-black.css', this.themeBlackText]
                    ,['xtheme-olive.css', this.themeOliveText]
                    ,['xtheme-purple.css', this.themePurpleText]
                    ,['xtheme-slate.css', this.themeSlateText]
                    ,['xtheme-peppermint.css', this.themePeppermintText]
                    ,['xtheme-chocolate.css', this.themeChocolateText]
                    ,['xtheme-silverCherry.css', this.themeSilverCherryText]
                ]
            })
            ,valueField: 'themeFile'
            ,displayField: 'themeName'
            ,triggerAction:'all'
            ,mode: 'local'
            ,listWidth: 100
            ,width: 100
            ,forceSelection:true
            ,editable:false
            ,fieldLabel: this.selectThemeText
        }); // end of apply

        // call parent
        Ext.ux.ThemeCombo.superclass.initComponent.apply(this, arguments);
        
        this.setValue(Ext.state.Manager.get(this.themeVar) || 'xtheme-default.css');
    } // end of function initComponent
    // }}}
    // {{{
    ,setValue:function(val) {
    	Ext.ux.ThemeCombo.superclass.setValue.apply(this, arguments);
    	// set theme
    	Ext.util.CSS.swapStyleSheet(this.themeVar, this.cssPath + val);
    	if(Ext.state.Manager.getProvider()) {
    		Ext.state.Manager.set(this.themeVar, val);
    	}
    }
    // }}}
    // {{{
    ,onSelect:function() {
        // call parent
        Ext.ux.ThemeCombo.superclass.onSelect.apply(this, arguments);
        
        // set theme
        var theme = this.getValue();
        Ext.util.CSS.swapStyleSheet('theme', this.cssPath + theme);

        if(Ext.state.Manager.getProvider()) {
            Ext.state.Manager.set('theme', theme);
        }
    } // end of function onSelect
    // }}}

}); // end of extend 
