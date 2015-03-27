function initialize() {
    var sName = 'blacklist';
    var sHeaderTitle = 'Blacklist';
    var sDocumentTitle = 'Bloqueo de contactos';
    var sTemplateName = 'Plugin_Blacklist';
    var oViewModelClass = Blacklist;

    window.AfterLogicApi.addScreenToHeader(sName, sHeaderTitle, sDocumentTitle, sTemplateName, oViewModelClass);
}

initialize();

Blacklist.prototype.isShowingSpinner = false;

Blacklist.prototype.showSpinner = function () {
    if (!this.isShowingSpinner) {
        this.isShowingSpinner = true;
        $('#bl_modal').dialog({ modal: true, height: 'auto', resizable: false, width: 350, closeText: '', bgiframe: true, closeOnEscape: false, dialogClass: 'no-close'});
    }
};

Blacklist.prototype.hideSpinner = function () {
    this.isShowingSpinner = false;
    $('#bl_modal').dialog('close');
};

Blacklist.prototype.addToBlacklistEmail = function () {
    var email = $('#emailToBlockInput').val();
    if (email != null && email.length > 0) {
        if (confirm('¿Seguro que deseas bloquear los emails hacia ' + email + '?')) {
            if (!this.isShowingSpinner) {
                this.showSpinner();
                App.ContactsCache.addToBlacklist(email, this.onAddToBlacklistResponse, this);
            }
        }
    }
};

Blacklist.prototype.onAddToBlacklistResponse = function (oResponse, oRequest) {
    if (oResponse.Result) {
        App.Api.showReport('Email bloqueado correctamente');
    } else {
        App.Api.showError('No se ha podido añadir a la lista de bloqueados.');
    }
    this.hideSpinner();
};

Blacklist.prototype.verifyEmailStatusOnBlacklist = function () {
    var email = $('#emailToVerifyInput').val();
    if (email != null && email.length > 0) {
        if (!this.isShowingSpinner) {
            this.showSpinner();
            var oParameters = {
                'Action': 'VerifyEmailStatusOnBlacklist',
                'Email': email
            };
            App.Ajax.send(oParameters, this.onVerifyEmailStatusOnBlacklistResponse, this);
        }
    }
};

Blacklist.prototype.sendFileToUploader = function () {
    this.showSpinner();
    $('#fileForUploadForm').submit();
};

Blacklist.prototype.onVerifyEmailStatusOnBlacklistResponse = function (oResponse, oRequest) {
    if (oResponse.Result) {
        App.Api.showReport('Email no bloqueado');
    } else {
        App.Api.showReport('Email en la lista de bloqueados');
    }
    this.hideSpinner();
};


Blacklist.prototype.blockDomain = function () {
    var domain = $('#domainToBlockInput').val();
    if (domain != null && domain.length > 4) {
        if (confirm('¿Seguro que deseas bloquear el dominio ' + domain + '?')) {
            if (!this.isShowingSpinner) {
                this.showSpinner();
                var oParameters = {
                    'Action': 'BlockDomain',
                    'Domain': domain
                };
                App.Ajax.send(oParameters, this.onBlockDomainResponse, this);
            }
        }
    }
};


Blacklist.prototype.onBlockDomainResponse = function (oResponse, oRequest) {
    if (oResponse.Result) {
        App.Api.showReport('Domino bloqueado correctamente');
    } else {
        App.Api.showError('No se ha podido bloquear el dominio');
    }
    this.hideSpinner();
};

Blacklist.prototype.reopenEmailOnBlacklist = function () {
    var email = $('#emailToReopen').val();
    var password = prompt('Seguro que quieres desbloquear el email ' + email + '?. Introduce la contraseña para continuar.');
    if (password == 'consultoriaDesbloquear123') {
        if (!this.isShowingSpinner) {
            this.showSpinner();
            var oParameters = {
                'Action': 'ReopenEmail',
                'Email': email
            };
            App.Ajax.send(oParameters, this.onReopenEmailBlacklist, this);
        }
    } else {
        alert('¡Contraseña incorrecta!');
    }
};


Blacklist.prototype.onReopenEmailBlacklist = function (oResponse, oRequest) {
    if (oResponse.Result) {
        App.Api.showReport('Email desbloqueado correctamente');
    } else {
        App.Api.showError('No se ha podido desbloquear el dominio');
    }
    this.hideSpinner();
};