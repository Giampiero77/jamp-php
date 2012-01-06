/**
* Class Language IT
* @author	Alyx Association <info@alyx.it>
* @version	1.0.1 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsLANG()
{
	this.package = {
		"CAL000"	: "CHIUDI",
		"CAL001"	: "OGGI",
		"CAL002"	: "ANNULLA",
		"CAL003"	: "CONFERMA",
		"JDS000"	: "Vuoi eliminare i dati?",
		"JDS001"	: "ATTENZIONE Vuoi eliminare TUTTI i dati?",
		"JDS002"	: "SEI SICURO di volere eliminare TUTTI i dati?",
		"JDS003"	: "Vuoi salvare le modifiche?",
		"JDS004"	: "Vuoi salvare i nuovi dati?",
		"JAJAX001"	: "Funzioni AJAX non supportate dal vostro Browser, vi consigliamo di utilizzare l'ultima versione di Firefox!",
		"EVENT001"	: "Errore JavaScript",
		"EVENT002"	: "Errore Transazione XML",
		"EVENT003"	: "Errore HTML",
		"EVENT004"	: "File:",
		"EVENT005"	: "Riga:",
		"EVENT006"	: "Messaggio:",
		"DSNAV001"	: "NUOVO",
		"IMAGE001"	: "Errore durante l'upload dell'immagine",
		"TEXT001"	: "Errore durante l'upload del file",
		"DOTPAD"	: ","
	}
}

clsLANG.prototype =
{
	translate : function (code)
	{
		if (this.package[code] == undefined) SYSTEMEVENT.errorJAVASCRIPT("Codice Errore: <b>"+ code + "</b> non definito!", "IT.js", "");
		else return this.package[code];
	}
}
var LANG = new clsLANG();
