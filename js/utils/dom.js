
export function createLabel(text, { id = null, classes = null } = {}) {
	let label = document.createElement("label");
	label.textContent = text;
	setIdClass(label, id, classes);
	return label;
}

export function createDiv({ id = null, classes = null } = {}) {
    let div = document.createElement("div");
    setIdClass(div, id, classes);
    return div;
}

export function createButton(type, text, { id = null, classes = null, title = null, disabled = false, readonly = false, toggleButton = {} = {} } = {}) {
	const button = document.createElement('button');
	setIdClass(button, id, classes);
	button.type = type;
	button.textContent = text;
	setDisabledReadOnlyRequired(button, disabled, readonly, false);
	if (toggleButton != null) {
		console.log("Non codé dom.js - createButton");
	}
	if (title != null) {
		button.title = title;
	}
	return button;
}

export function createInput(type, name = null, 
		{placeholder = null, value = null, id = null, classes = null, disabled = false, maxlength = null, required = true, readonly = false,
			turnOffAutoComplete = true, title = null
		} = {}) {
    const input = document.createElement('input');
    input.type = type;
	setName(input, name);
	setIdClass(input, id, classes);
	setPlaceholder(input, placeholder);
	setTitle(input, title);
	setValue(input, value);
	setMaxlength(input, maxlength);
	setDisabledReadOnlyRequired(input, disabled, readonly, required);
	setTurnOffAutoComplete(input, turnOffAutoComplete);
    return input;
}

export function prepareDateInput(dateInput, date, flatpickrized = true) {
	feedDateInput(dateInput, date);
	if (flatpickrized) {
		flatpickrize(dateInput);
	}
}

export function flatpickrize(inputText) {
	flatpickr(inputText, { 
		enableTime: true,
		dateFormat: "d/m/Y H:i",
		locale: "fr",
		allowInput: true
	});
}

export function feedDateInput(dateInput, now) {
	// Format YYYY-MM-DDTHH:MM
	const year = now.getFullYear();
	const month = String(now.getMonth() + 1).padStart(2, '0');
	const day = String(now.getDate()).padStart(2, '0');
	const hours = String(now.getHours()).padStart(2, '0');
	const minutes = String(now.getMinutes()).padStart(2, '0');
	dateInput.value = day + '/' + month + '/' + year + ' ' + hours + ':' + minutes;
}

export function createTextArea(name, 
		{ placeholder = null, value = null, id = null, classes = null, disabled = false, rows = null, maxlength = null, required = true, readonly = false } = {}) {
    const input = document.createElement('textarea');
    input.name = name;
	setIdClass(input, id, classes);
	setPlaceholder(input, placeholder);
	setValue(input, value);
	setMaxlength(input, maxlength);
	if (rows != null) {
		input.rows = rows;
	}
	setDisabledReadOnlyRequired(input, disabled, readonly, required);
    return input;
}

export function createImg(src, alt, title, { id = null, classes = null }) {
	const img = document.createElement("img");
	setIdClass(img, id, classes);
	img.src = src;
	img.alt = alt;
	img.title = title;
	return img;
}

export function createIconButton(type, alternativeText, toolTipText, image, callback, { id = null, classes = null, disabled = false, readonly = false, imgId = null, imgClasses = null } = {}) {
    const button = createButton(type, null, {id : id, classes : classes, disabled : disabled, readonly : readonly } );
	button.classList.add("iconButton");
	const img = createImg(image, alternativeText, toolTipText, { id : imgId, classes : imgClasses });
	button.appendChild(img);
	button.addEventListener("click", () => {
		callback();
	});
    return button;
}


export function createToogleButton(type, alternativeText, toolTipText, toggleImages, callbackArmed, callbackNotArmed, { id = null, classes = null, disabled = false, readonly = false, imgId = null, imgClasses = null } = {}) {
    const button = createButton(type, null, {id : id, classes : classes, disabled : disabled, readonly : readonly } );
	button.classList.add("iconButton");
	button.classList.add("notArmedButton");
	let toggleState = 0;
	const img = createImg(toggleImages[toggleState], alternativeText, toolTipText, { id : imgId, classes : imgClasses });
	button.appendChild(img);
	button.addEventListener("click", () => {
		toggleState = 1 - toggleState;
		img.src = toggleImages[toggleState];
		if (toggleState == 0) {
			button.classList.remove("armedButton");
			button.classList.add("notArmedButton");
			callbackNotArmed();
		} else {
			button.classList.remove("notArmedButton");
			button.classList.add("armedButton");
			callbackArmed();
		}
	});
    return button;
}

export function createForm( { method = "get", action = "api.php", enctype = "application/x-www-form-urlencoded", id = null, classes = null, 
		turnOffAutoComplete = true}  = {} ) {
	const form = document.createElement("form");
	form.method = method;
	form.action = action;
	form.enctype = enctype;
	setIdClass(form, id, classes);
	setTurnOffAutoComplete(form, turnOffAutoComplete);
	return form;
}

export function createH1(textContent, { id = null, classes = null }  = {} ) {
	return createHN(1, textContent, { id : id, classes : classes } );
}

export function createH2(textContent, { id = null, classes = null }  = {} ) {
	return createHN(2, textContent, { id : id, classes : classes } );
}

export function createH3(textContent, { id = null, classes = null }  = {} ) {
	return createHN(3, textContent, { id : id, classes : classes } );
}

export function createH4(textContent, { id = null, classes = null }  = {} ) {
	return createHN(4, textContent, { id : id, classes : classes } );
}

export function createH5(textContent, { id = null, classes = null }  = {} ) {
	return createHN(5, textContent, { id : id, classes : classes } );
}

export function createH6(textContent, { id = null, classes = null }  = {} ) {
	return createHN(6, textContent, { id : id, classes : classes } );
}

function createHN(N, textContent, { id = null, classes = null }  = {} ) {
	const hN = document.createElement("h" + N);
	hN.textContent = textContent;
	setIdClass(hN, id, classes);
	return hN;
}

export function createLi(textContent, { id = null, classes = null }  = {} ) {
	const li = document.createElement("li");
	if (textContent != null) {
		li.textContent = textContent;	
	}
	setIdClass(li, id, classes);
	return li;
}

export function createUl({ id = null, classes = null }  = {} ) {
	const ul = document.createElement("ul");
	setIdClass(ul, id, classes);
	return ul;
}

export function createA(textContent, { id = null, classes = null, href = null, callback = null }  = {} ) {
	const a = document.createElement("a");
	a.textContent = textContent;
	setIdClass(a, id, classes);
	if (href != null) {
		a.href = href;
	}
	if (callback != null) {
		a.addEventListener('click', function(event) {
			event.preventDefault();
			callback(event);
		});
	}
	return a;
}

export function createTable(columns, { id = null, classes = null }  = {}) {
    const table = document.createElement('table');
	setIdClass(table, id, classes);
    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
    columns.forEach(col => {
        let th = document.createElement('th');
        th.textContent = col;
        headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);
    const tbody = document.createElement('tbody');
    table.appendChild(tbody);
    return table;
}

export function createSelect(name, options, valueExtractorCallBack, textExtractorCallBack, 
		{ id = null, classes = null, placeHolder = null, selectPlaceholder = null, selectedValue = null, 
			disabled = false, title = null, required = true, readonly = false, addStar = true,
			turnOffAutoComplete = true }  = {}) {
	const select = document.createElement('select');
	select.name = name;
	setPlaceholder(select, placeHolder);
	setTitle(select, title);
	
	if(selectPlaceholder != null) {
		// <option value="" disabled selected hidden>— Sélectionnez une option —</option>
		const placeholderOption = document.createElement('option');
		placeholderOption.value = "";
		placeholderOption.disabled = true;
		placeholderOption.selected = true;
		placeholderOption.textContent = selectPlaceholder;
		select.appendChild(placeholderOption);
	}
	setIdClass(select, id, classes);
	setDisabledReadOnlyRequired(select, disabled, readonly, required);
	options.forEach(data => {
		const option = document.createElement('option');
		let value = valueExtractorCallBack(data);
		option.value = value;
		if (value == selectedValue) {
			option.selected = true;
			option.textContent = textExtractorCallBack(data) + (addStar ? "*" : "");
		} else {
			option.textContent = textExtractorCallBack(data);
		}
		select.appendChild(option);
	});
	setTurnOffAutoComplete(select, turnOffAutoComplete);
	return select;
}

/**
 * @param startZero boolean true, index in months is zero, shift + 1 for real index 1-12
 */
export function createSelectMonths(months, defaultValue, { startZero = true, addStar = true } = {}) {
	return createSelect("month",
		months.map(([key, value]) => ({ label: value, value: key })),
		item => parseInt(item.value) + (startZero ? +1 : 0), // extraction de la value pour le <option>
		item => item.label, // extraction du label pour le <option>
		{
			id: "select-month",
			classes: "textCenter selectDate",
			selectedValue : defaultValue + (startZero ? +1 : 0),
			addStar : addStar
		}
	);
}

export function createSelectYear(years, defaultValue, { addStar = true } = {}) {
	return createSelect("year",
		years,
		item => item,
		item => item,
		{
			id: "select-year",
			classes: "textCenter selectDate",
			selectedValue : defaultValue,
			addStar : addStar
		}
	);
}

export function createSpan(textContent, { id = null, classes = null } = {}) {
    const span = document.createElement("span");
    span.textContent = textContent;
	setIdClass(span, id, classes);
    return span;
}

////////////////////////////////////////////////////////////////////////////////
//                            Boite à outils                                  //
////////////////////////////////////////////////////////////////////////////////
function setIdClass(element, id = null, classes = null ) {
	if (id != null) {
		element.id = id;
	}
	if (classes != null) {
		element.className = classes;
	}
}

function setDisabledReadOnlyRequired(input, disabled, readonly, required) {
	if (readonly) {
		input.setAttribute("readonly", "");
	}
	input.disabled = disabled;
	if (required) {
		input.setAttribute("required", "");
	}
}

function setMaxlength(input, maxlength) {
	if (maxlength != null) {
		input.maxlength = maxlength;
	}
}

function setValue(input, value) {
	if (value != null) {
	    input.value = value;
	}
}	

function setTitle(input, title) {
	if (title != null) {
		input.title = title;
	}
}

function setPlaceholder(input, placeholder) {
	if (placeholder != null) {
	    input.placeholder = placeholder;
	}
}

function setName(input, name) {
	if (name != null) {
		input.name = name;
	}
}

function setTurnOffAutoComplete(input, turnOff) {
	if (turnOff) {
		input.setAttribute('autocomplete', "off");
		input.setAttribute('autofill', "off");
		input.setAttribute('aria-autocomplete', "none");
	}	
}

////////////////////////////////////////////////////////////////////////////////
//                              Tabs panel                                    //
////////////////////////////////////////////////////////////////////////////////
export function createTabPanelDiv(titles, contents, { id = "tabs", classes = "", tabIdPrefix = "tabs" }  = {}) {
	if (titles.length != contents.length) {
		throw new Error("titles.length and contents.length mismatch");
	}
	const tabs = createDiv({ id : id, classes : "ui-tabs ui-corner-all ui-widget ui-widget-content " + classes });
	const ul = createUl({ classes : "ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header "});
	tabs.appendChild(ul);
	for (let i = 0; i < titles.length; i++) {
		// Liste à lien qui permet de naviguer dans les différents onglets
		const theId = tabIdPrefix + "-" + i;		
		const li = createLi(null, { classes : "ui-tabs-tab ui-corner-top ui-state-default " } ) 
		const a = createA(titles[i], { href : "#" + theId}); 
		li.appendChild(a);
		ul.appendChild(li);
		// Div qui acueille les inputs
		const div = createDiv( { id : theId, classes : "ui-tabs-panel ui-corner-bottom ui-widget-content " } );
		div.appendChild(contents[i]);
		tabs.appendChild(div);
	}
	return tabs;
} 

////////////////////////////////////////////////////////////////////////////////
//                    JQuery et chargements dynamiques                        //
////////////////////////////////////////////////////////////////////////////////
