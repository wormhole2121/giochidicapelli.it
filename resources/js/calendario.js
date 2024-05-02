let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let selectedDate = null;

const params = new URLSearchParams(window.location.search);
if (params.has('date')) {
    selectedDate = new Date(params.get('date'));
    currentMonth = selectedDate.getMonth();
    currentYear = selectedDate.getFullYear();
}

function updateCurrentMonth() {
    const monthNames = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];
    document.getElementById("currentMonth").textContent = monthNames[currentMonth] + ' ' + currentYear;
}

function changeMonth(delta) {
    currentMonth += delta;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    } else if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    updateCurrentMonth();
    generateDays();
}

document.getElementById("prevMonth").addEventListener("click", () => {
    changeMonth(-1);
});

document.getElementById("nextMonth").addEventListener("click", () => {
    changeMonth(1);
});

window.addEventListener("DOMContentLoaded", () => {
    updateCurrentMonth();
    generateDays();
});

function generateDays() {
    const datesContainer = document.getElementById("dates");
    datesContainer.innerHTML = '';

    const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
    const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);

    const currentDate = new Date();
    const currentMonthNow = currentDate.getMonth();
    const currentYearNow = currentDate.getFullYear();

    const isPastMonth = currentYear < currentYearNow || (currentYear === currentYearNow && currentMonth < currentMonthNow);
    const isCurrentMonth = currentYear === currentYearNow && currentMonth === currentMonthNow;

    let startingDay = firstDayOfMonth.getDay();
    for (let i = 0; i < startingDay; i++) {
        const emptyDateElement = document.createElement("div");
        emptyDateElement.classList.add("date", "out-of-month");
        datesContainer.appendChild(emptyDateElement);
    }

    const nonBookableDates = {
        5: [13, 14, 15], // Giugno
        6: [2, 3, 4, 5, 6, 7, 8, 9, 10], // Luglio
        7: [13, 14, 15, 16, 17] // Agosto
    };

    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
        const date = new Date(currentYear, currentMonth, i);
        const dateElement = document.createElement("div");
        dateElement.classList.add("date");
        dateElement.setAttribute('data-date', `${currentYear}-${currentMonth + 1}-${i}`);
    
        const spanElement = document.createElement("span");
        spanElement.textContent = i;
        dateElement.appendChild(spanElement);

        const isNonBookable = currentYear === 2024 && nonBookableDates[currentMonth] && nonBookableDates[currentMonth].includes(i);
        if (isPastMonth || (isCurrentMonth && i < currentDate.getDate()) || isNonBookable) {
            dateElement.classList.add("non-selectable");
        } else {
            const dayOfWeek = date.getDay();
            if (dayOfWeek !== 0 && dayOfWeek !== 1) {
                dateElement.addEventListener("click", () => {
                    window.location.search = `?date=${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`;
                });
            } else {
                dateElement.classList.add("non-selectable");
            }
        }
    
        datesContainer.appendChild(dateElement);
    
        if (selectedDate && i === selectedDate.getDate() && currentMonth === selectedDate.getMonth() && currentYear === selectedDate.getFullYear()) {
            dateElement.classList.add('active');
        }
    }
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








// function generateDays() {
//     const datesContainer = document.getElementById("dates");
//     datesContainer.innerHTML = '';

//     const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
//     const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);

//     let startingDay = firstDayOfMonth.getDay();
//     for (let i = 0; i < startingDay; i++) {
//         const emptyDateElement = document.createElement("div");
//         emptyDateElement.classList.add("date", "out-of-month");
//         datesContainer.appendChild(emptyDateElement);
//     }

//     for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
//         const date = new Date(currentYear, currentMonth, i);
//         const dayOfWeek = date.getDay();
//         const dateElement = document.createElement("div");
//         dateElement.classList.add("date");
//         dateElement.setAttribute('data-date', `${currentYear}-${currentMonth + 1}-${i}`);

//         const spanElement = document.createElement("span");
//         spanElement.classList.add("day");
//         spanElement.textContent = i;

//         if (dayOfWeek !== 0 && dayOfWeek !== 1) {
//             // Solo i giorni che non sono domenica (0) o lunedì (1) sono selezionabili
//             dateElement.addEventListener("click", () => {
//                 window.location.search = `?date=${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`;
//             });
//         } else {
//             // Applica una classe CSS per giorni non selezionabili (ad esempio, 'non-selectable')
//             dateElement.classList.add("non-selectable");
//         }

//         dateElement.appendChild(spanElement);
//         datesContainer.appendChild(dateElement);

//         if (selectedDate && i === selectedDate.getDate() && currentMonth === selectedDate.getMonth() && currentYear === selectedDate.getFullYear()) {
//             dateElement.classList.add('active');
//         }
//     }
// }