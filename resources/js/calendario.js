let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

function updateCurrentMonth() {
    const monthNames = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];
    document.getElementById("currentMonth").textContent = monthNames[currentMonth] + ' ' + currentYear;
}

document.getElementById("prevMonth").addEventListener("click", () => {
    if (currentMonth > 0) {
        currentMonth--;
    } else {
        currentMonth = 11;
        currentYear--;
    }
    updateCurrentMonth();
    generateDays();
});

document.getElementById("nextMonth").addEventListener("click", () => {
    if (currentMonth < 11) {
        currentMonth++;
    } else {
        currentMonth = 0;
        currentYear++;
    }
    updateCurrentMonth();
    generateDays();
});

window.addEventListener("DOMContentLoaded", (event) => {
    document.querySelector(".card");
    updateCurrentMonth();
    generateDays();
});

function generateDays() {
    const datesContainer = document.getElementById("dates");
    datesContainer.innerHTML = '';

    const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
    const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);

    // Aggiungere date vuote per allineare il primo giorno del mese
    let startingDay = firstDayOfMonth.getDay();
    for (let i = 0; i < startingDay; i++) {
        const prevDateElement = document.createElement("div");
        prevDateElement.classList.add("date", "out-of-month");

        const spanElement = document.createElement("span");
        spanElement.classList.add("day");
        spanElement.textContent = ""; 

        prevDateElement.appendChild(spanElement);
        datesContainer.appendChild(prevDateElement);
    }

    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
        const dateElement = document.createElement("div");
        dateElement.classList.add("date");

        const spanElement = document.createElement("span");
        spanElement.classList.add("day");
        spanElement.textContent = i;

        // Creare una nuova data per controllare il giorno della settimana
        const currentDay = new Date(currentYear, currentMonth, i).getDay();

        // Se è domenica o lunedì, rendi la data non selezionabile
        if (currentDay === 0 || currentDay === 1) {
            spanElement.classList.add("non-selectable"); // Aggiungi la tua classe CSS qui
            dateElement.classList.add("non-selectable"); // Opzionale: aggiungi anche la classe al contenitore se necessario
        } else {
            // Aggiungi un gestore di eventi solo se la data è selezionabile
            dateElement.addEventListener("click", () => {
                const selectedDate = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`;
                updateDateDropdown(selectedDate);
                document.querySelector('.btn-show-bookings').click();
            });
        }

        if (currentYear === new Date().getFullYear() && currentMonth === new Date().getMonth() && i === new Date().getDate()) {
            spanElement.classList.add("today");
        }

        dateElement.appendChild(spanElement);
        datesContainer.appendChild(dateElement);
    }

    // Aggiungi le date vuote per allineare l'ultimo giorno del mese
    let endingDay = 6 - lastDayOfMonth.getDay();
    for (let i = 1; i <= endingDay; i++) {
        const nextDateElement = document.createElement("div");
        nextDateElement.classList.add("date", "out-of-month");

        const spanElement = document.createElement("span");
        spanElement.classList.add("day");
        spanElement.textContent = ""; 

        nextDateElement.appendChild(spanElement);
        datesContainer.appendChild(nextDateElement);
    }
}


function deselectAllDays() {
    document.querySelectorAll(".calendar .date.selected").forEach(el => {
        el.classList.remove("selected");
    });
}

function updateDateDropdown(selectedDate) {
    const dropdown = document.querySelector(".date-dropdown");
    let optionExists = false;

    // Verifica se l'opzione esiste già
    dropdown.querySelectorAll("option").forEach(option => {
        if (option.value === selectedDate) {
            option.selected = true;
            optionExists = true;
        } else {
            option.selected = false;
        }
    });

    // Se l'opzione non esiste, la crea e la seleziona
    if (!optionExists) {
        const optionElement = document.createElement("option");
        optionElement.value = selectedDate;
        optionElement.selected = true;
        optionElement.textContent = selectedDate;

        dropdown.appendChild(optionElement);
    }
}

$(document).ready(function() {
    $("#haircut_types").select2({
        width: 'resolve'
    });
});


document.addEventListener('DOMContentLoaded', function() {
    let timeButtons = document.querySelectorAll('.time-btn');
    let selectedTimeInput = document.getElementById('selectedTime');

    timeButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Deseleziona tutti gli altri bottoni
            timeButtons.forEach(function(innerBtn) {
                innerBtn.classList.remove('btn-primary');
                innerBtn.classList.add('btn-outline-primary');
            });

            // Seleziona il bottone cliccato
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-primary');

            // Imposta l'orario selezionato nell'input nascosto
            selectedTimeInput.value = btn.getAttribute('data-time');
        });
    });

    // Controlla se c'è un orario già selezionato e seleziona il bottone corrispondente
    if (selectedTimeInput.value) {
        let selectedButton = document.querySelector(`.time-btn[data-time="${selectedTimeInput.value}"]`);
        if (selectedButton) {
            selectedButton.click(); // Simula un clic sul bottone per selezionarlo
        }
    }
});


