/**
* Class Language IT
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsLANG()
{
	this.package = {
		"CAL000"	: "ÎNCHIDE",
		"CAL001"	: "ASTĂZI",
		"CAL002"	: "ANULLA",
		"CAL003"	: "CONFIRMARE",
		"JDS000"	: "Doriţi să ştergeţi datele?",
		"JDS001"	: "ATENŢIE TOATE datele pe care doriţi să ştergeţi?",
		"JDS002"	: "Sigur doriţi să ştergeţi toate datele?",
		"JDS003"	: "A salva modificările?",
		"JDS004"	: "A salva date noi?",
		"JAJAX001"	: "AJAX funcţii nu sunt acceptate de browser-ul dvs., vă recomandăm să utilizaţi cea mai recentă versiune de Firefox!",
		"EVENT001"	: "JavaScript Eroare",
		"EVENT002"	: "XML tranzacţiei Eroare",
		"EVENT003"	: "HTML Eroare",
		"EVENT004"	: "File:",
		"EVENT005"	: "Riga:",
		"EVENT006"	: "Mesaj:",
		"DSNAV001"	: "NOU",
		"DSNAV002"	: "CAUTA",
		"DSNAV003"	: "Vrei imprimare?",
		"DSNAV004"	: "Vrei export?",
		"IMAGE001"	: "Eroare în timp ce încărcaţi imagini",
		"TEXT001"	: "Eroare la încărcarea fişierului",
		"DOTPAD"	: ","
	};
}

clsLANG.prototype =
{
	translate : function (code)
	{
		if (this.package[code] == undefined) SYSTEMEVENT.errorJAVASCRIPT("Codice Errore: <b>"+ code + "</b> non definito!", "IT.js", "");
		else return this.package[code];
	}
};
var LANG = new clsLANG();
