// Repository: https://github.com/MarvinLudwig/EPC069-12_for_Billomat
// Licence: Apache 2.0
// Version 1.0

var UI =  {
	checkAll : function (e){
		var allBoxes = document.querySelectorAll(".chkBox");
		if (e.target.checked){
			allBoxes.forEach(function(box){
				if (!box.disabled) box.checked = true;
			}); 	
		}	
		else {
			allBoxes.forEach(function(box){box.checked = false;}); 	
		}
	}
}

var FUNC =  {
	actions : {SHOW_QR : "SHOW_QR", SEND_MAIL : "SEND_MAIL"},
	sendMail : function (e){
		FUNC.sendMail2(e.target);
	},
	sendMail2 : function (target){
		if (target.localName == "td"){ // send only one invoice
			var parent = target.parentNode;
			var invoice_id = parent.attributes["data-invoice_id"].value;
			var errorTD = document.querySelector("#invoice_"+invoice_id).querySelector(".error");
			errorTD.innerHTML = "";
			var sendmailTD = document.querySelector("#invoice_"+invoice_id).querySelector(".sendmail");
			sendmailTD.innerHTML = "<img src='load.gif' width='30'>";
			var invoice = all_invoices[invoice_id];
			invoice.client_email = parent.querySelector('[type="email"]').value;
			var invoices = {invoice};			
		}
		else { // send all selected invoices
			var invoices_tr = document.querySelector("#invoices").childNodes;
			var invoices = new Array();
			invoices_tr.forEach(function(invoice_tr){
				if (invoice_tr.querySelector('[type="checkbox"]').checked == true){
					var invoice_id = invoice_tr.attributes["data-invoice_id"].value;
					var errorTD = document.querySelector("#invoice_"+invoice_id).querySelector(".error");
					errorTD.innerHTML = "";
					var sendmailTD = document.querySelector("#invoice_"+invoice_id).querySelector(".sendmail");
					sendmailTD.innerHTML = "<img src='load.gif' width='30'>";
					var invoice = all_invoices[invoice_id];
					invoice.client_email = invoice_tr.querySelector('[type="email"]').value;
					invoices.push(invoice);	
				}
			});
		}
		FUNC.call(FUNC.actions.SEND_MAIL,invoices,FUNC.sendMail_response);
	},	
	showQR : function (e){
		FUNC.showQR2(e.target);
	},
	showQR2 : function (target){ // request qr code and show the result
		var invoice_id = target.parentNode.attributes["data-invoice_id"].value;
		var invoice = all_invoices[invoice_id];
		var invoices = {invoice};			
		FUNC.call(FUNC.actions.SHOW_QR,invoices,FUNC.showQR_response);
	},
	sendMail_response : function (response){
		var invoices = JSON.parse(response);
		invoices.forEach(function(invoice){
			if (invoice.result == "OK" && invoice.error == undefined){
				var sendmailTD = document.querySelector("#invoice_"+invoice.id).querySelector(".sendmail");
				sendmailTD.innerHTML = "&#xf00c;";
				sendmailTD.className += " ok";
				sendmailTD.removeEventListener("click",FUNC.sendMail);				
				var chkBox = document.querySelector("#invoice_"+invoice.id).querySelector('[type="checkbox"]');
				chkBox.checked = false;
				chkBox.disabled = true;
			}
			else {
				var sendmailTD = document.querySelector("#invoice_"+invoice.id).querySelector(".sendmail");
				sendmailTD.innerHTML = "&#xf1d9;";
				sendmailTD.className = "sendmail";
				FUNC.handleError(invoice,FUNC.sendMail2);
			}
		});
	},	
	showQR_response : function (response){
		var invoices = JSON.parse(response);
		var invoice = invoices[0];
			if (invoice.result == "OK" && invoice.error == undefined){ // show QR code
				var qrImg = document.querySelector("#qrImg");
				var qr = invoice.details;
				if (qrImg != undefined){
						qrImg.src = "data:image/png;base64,"+qr;
						document.querySelector("#qrDiv").style.visibility="visible";
				}
				else {
					var qrImg = document.createElement("img");
					qrImg.id = "qrImg";
					qrImg.src = "data:image/png;base64,"+qr;
					var qrDiv = document.createElement("div");
					qrDiv.id = "qrDiv";
					qrDiv.appendChild(qrImg);
					document.body.appendChild(qrDiv);
					attachEvent("html","click",function(){document.querySelector("#qrDiv").style.visibility="hidden";});
				}
			}
			else {
				FUNC.handleError(invoice,FUNC.showQR2);
			}
	},
	handleError(invoice,func){
		var error = invoice.error;
		var errorTD = document.querySelector("#invoice_"+invoice.id).querySelector(".error");
		if (error.code == 1){ // simple error output
			errorTD.innerHTML = error.message;
		}
		else if (error.code == 2){ // we need input from user
			var confirm_cut = false;
			if (CUT_PAYMENT_REFERENCE == 0) confirm_cut = confirm(error.message+" "+PAYLOAD_BYTE_ERROR_MSG_1+"\r\n"+error.details+"\r\n"+PAYLOAD_BYTE_ERROR_MSG_2); 
			if (CUT_PAYMENT_REFERENCE == 1 || confirm_cut == true) {
				all_invoices[invoice.id].payment_reference = error.details;
				func(document.querySelector("#invoice_"+invoice.id).querySelector(".sendmail"));
			}
		}			
	},
	call : function(action, invoices, onresponse){
		var iban = document.querySelector("#iban").innerHTML;
		var bic = document.querySelector("#bic").innerHTML;
		var name = document.querySelector("#name").innerHTML;
		var payload = {"action" : action, "iban" : iban, "bic" : bic, "name" : name, "invoices": invoices};
		var ajax = new XMLHttpRequest();
		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==3)
			{
				var newResponse = ajax.responseText.replace(FUNC.previousResponse,"");
				newResponse = newResponse.replace("}][{","},{");
				FUNC.previousResponse = ajax.responseText;
				if (newResponse != "") onresponse(newResponse);
			}
		}
		ajax.open("POST","billomat_epc06912.php",true);
		ajax.send(JSON.stringify(payload));
	}
}

// attach Events
window.addEventListener('DOMContentLoaded', function ()
{
	NodeList.prototype.forEach = Array.prototype.forEach;
	attachEvent("#chkAll","change",UI.checkAll);
	attachEvent(".sendmail","click",FUNC.sendMail); 
	attachEvent(".showqr","click",FUNC.showQR);
});
	
function attachEvent (query, event, handler){
	document.querySelectorAll(query).forEach(function(node){node.addEventListener(event,handler);});	
}




