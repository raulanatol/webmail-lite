function initialize() {
    var sName = 'blacklist';
    var sHeaderTitle = 'Blacklist';
    var sDocumentTitle = 'blacklist Title';
    var sTemplateName = 'Plugin_Blacklist';
    var oViewModelClass = Blacklist;

    window.AfterLogicApi.addScreenToHeader(sName, sHeaderTitle, sDocumentTitle, sTemplateName, oViewModelClass);
}

initialize();