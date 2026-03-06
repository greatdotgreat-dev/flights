/**
 * ============================================================
 * BOOKING FORM - Complete JavaScript Logic
 * Flight CRM Agent Panel
 * ============================================================
 */

// ── Global State ─────────────────────────────────────────────
let passengerCounts = { adults: 1, children: 0, infants: 0 };
let cardCount = 1;
let segmentCount = 0;
let currentFlightType = '';

// ── Constants ────────────────────────────────────────────────
const cabinOptions = `
    <option value="economy">Economy</option>
    <option value="premium_economy">Premium Economy</option>
    <option value="business">Business</option>
    <option value="first">First Class</option>
`;

// ══════════════════════════════════════════════════════════════
// PASSENGER MANAGEMENT
// ══════════════════════════════════════════════════════════════

function changePassengerCount(type, delta) {
    const currentCount = passengerCounts[type];
    const newCount = currentCount + delta;

    // Validation
    if (type === 'adults') {
        if (newCount < 1) {
            alert('At least 1 adult is required');
            return;
        }
        if (newCount > 9) {
            alert('Maximum 9 adults allowed');
            return;
        }
    } else {
        if (newCount < 0) return;
        if (newCount > 9) {
            alert(`Maximum 9 ${type} allowed`);
            return;
        }
    }

    // Check total passengers
    const totalWithChange =
        (type === 'adults' ? newCount : passengerCounts.adults) +
        (type === 'children' ? newCount : passengerCounts.children) +
        (type === 'infants' ? newCount : passengerCounts.infants);

    if (totalWithChange > 9) {
        alert('Total passengers cannot exceed 9');
        return;
    }

    // Check infants not exceeding adults
    if (type === 'infants' && newCount > passengerCounts.adults) {
        alert('Number of infants cannot exceed number of adults');
        return;
    }

    if (type === 'adults' && newCount < passengerCounts.infants) {
        alert('Number of adults cannot be less than number of infants');
        return;
    }

    passengerCounts[type] = newCount;
    updatePassengerUI();
}

function updatePassengerUI() {
    // Update counters
    document.getElementById('adultsCount').textContent = passengerCounts.adults;
    document.getElementById('childrenCount').textContent = passengerCounts.children;
    document.getElementById('infantsCount').textContent = passengerCounts.infants;

    // Update hidden inputs
    document.getElementById('adults').value = passengerCounts.adults;
    document.getElementById('children').value = passengerCounts.children;
    document.getElementById('infants').value = passengerCounts.infants;

    // Update total
    const total = passengerCounts.adults + passengerCounts.children + passengerCounts.infants;
    document.getElementById('totalPassengers').textContent = total;

    // Generate passenger forms
    generatePassengerForms();
}

function generatePassengerForms() {
    const container = document.getElementById('passengersContainer');
    container.innerHTML = '';
    let passengerIndex = 0;

    // Add adults
    for (let i = 0; i < passengerCounts.adults; i++) {
        container.appendChild(createPassengerForm(passengerIndex, 'ADT', 'Adult', i + 1));
        passengerIndex++;
    }

    // Add children
    for (let i = 0; i < passengerCounts.children; i++) {
        container.appendChild(createPassengerForm(passengerIndex, 'CHD', 'Child', i + 1));
        passengerIndex++;
    }

    // Add infants
    for (let i = 0; i < passengerCounts.infants; i++) {
        container.appendChild(createPassengerForm(passengerIndex, 'INF', 'Infant', i + 1));
        passengerIndex++;
    }
}

function createPassengerForm(index, type, label, number) {
    const div = document.createElement('div');
    div.className = 'passenger-card';
    div.innerHTML = `
        <div class="passenger-card-header">
            <i class="fas fa-user"></i> ${label} ${number}
        </div>
        <input type="hidden" name="passengers[${index}][passenger_type]" value="${type}">
        
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label>Title <span class="text-danger">*</span></label>
                    <select class="form-control form-control-sm" name="passengers[${index}][title]" required>
                        <option value="Mr">Mr</option>
                        <option value="Mrs">Mrs</option>
                        <option value="Ms">Ms</option>
                        <option value="Miss">Miss</option>
                        <option value="Dr">Dr</option>
                        <option value="Master">Master</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" name="passengers[${index}][first_name]" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Middle Name</label>
                    <input type="text" class="form-control form-control-sm" name="passengers[${index}][middle_name]">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" name="passengers[${index}][last_name]" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Gender <span class="text-danger">*</span></label>
                    <select class="form-control form-control-sm" name="passengers[${index}][gender]" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" name="passengers[${index}][dob]" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Passport Number</label>
                    <input type="text" class="form-control form-control-sm" name="passengers[${index}][passport_number]">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Passport Expiry</label>
                    <input type="date" class="form-control form-control-sm" name="passengers[${index}][passport_expiry]">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Nationality</label>
                    <input type="text" class="form-control form-control-sm" name="passengers[${index}][nationality]">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Seat Preference</label>
                    <input type="text" class="form-control form-control-sm" name="passengers[${index}][seat_preference]" placeholder="e.g., Window, Aisle">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Meal Preference</label>
                    <input type="text" class="form-control form-control-sm" name="passengers[${index}][meal_preference]" placeholder="e.g., Vegetarian, Halal">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Special Assistance</label>
                    <input type="text" class="form-control form-control-sm" name="passengers[${index}][special_assistance]" placeholder="e.g., Wheelchair">
                </div>
            </div>
        </div>
    `;
    return div;
}

// ══════════════════════════════════════════════════════════════
// FLIGHT SEGMENTS MANAGEMENT
// ══════════════════════════════════════════════════════════════

function handleFlightTypeChange() {
    const flightTypeEl = document.getElementById('flighttype');      // ← must match blade id
    currentFlightType = flightTypeEl.value;

    const container  = document.getElementById('segmentscontainer');  // ← must match blade id
    const hint       = document.getElementById('flighttypehint');     // ← must match blade id
    const addWrapper = document.getElementById('addsegmentwrapper');  // ← must match blade id

    container.innerHTML = '';
    segmentCount = 0;

    if (!currentFlightType) {
        container.style.display  = 'none';
        hint.style.display       = 'block';
        addWrapper.style.display = 'none';
        return;
    }

    hint.style.display      = 'none';
    container.style.display = 'block';

    if (currentFlightType === 'oneway') {
        addWrapper.style.display = 'none';
        addSegment();
    } else if (currentFlightType === 'roundtrip') {
        addWrapper.style.display = 'none';
        addSegment();
    } else if (currentFlightType === 'multicity') {
        addWrapper.style.display = 'block';
        addSegment();
        addSegment();
    }
}


function addSegment() {
    if (segmentCount >= 6) {
        alert('Maximum 6 flight segments allowed.');
        return;
    }

    const index = segmentCount;
    const segNum = index + 1;
    const isFirst = index === 0;
    const showReturn = (currentFlightType === 'roundtrip' && isFirst);
    const isMulti = (currentFlightType === 'multicity');
    const canRemove = isMulti && index >= 2; // can remove from 3rd onwards

    const html = buildSegmentHTML(index, segNum, showReturn, isMulti, canRemove);
    document.getElementById('segmentscontainer').insertAdjacentHTML('beforeend', html);
    segmentCount++;

    // Pre-fill from_city of new segment from prev segment's to_city (UX helper)
    if (index > 0) {
        const prevTo = document.getElementById(`seg_to_${index - 1}`);
        const currFrom = document.getElementById(`seg_from_${index}`);
        if (prevTo && currFrom && prevTo.value) {
            currFrom.value = prevTo.value;
        }
        // Listen on prev to_city to auto-fill current from_city
        if (prevTo) {
            prevTo.addEventListener('input', function() {
                if (currFrom) currFrom.value = this.value;
            });
        }
    }
}

function buildSegmentHTML(index, segNum, showReturn, isMulti, canRemove) {
    const titleColor = isMulti ? 'bg-info' : 'bg-success';
    const titleText = isMulti 
        ? `<i class="fas fa-route mr-2"></i>Segment ${segNum}`
        : (showReturn 
            ? '<i class="fas fa-exchange-alt mr-2"></i>Flight Route' 
            : '<i class="fas fa-plane mr-2"></i>Flight Route');

    const removeBtn = canRemove 
        ? `<button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="removeSegment(${index})">
            <i class="fas fa-trash"></i> Remove
           </button>`
        : '';

    const returnDateField = showReturn 
        ? `<div class="col-md-3">
                <div class="form-group">
                    <label>Return Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="segments[${index}][return_date]" 
                           id="seg_return_${index}" required>
                </div>
           </div>`
        : '';

    return `
    <div class="segment-block border rounded mb-3" id="segment_block_${index}">
        <div class="${titleColor} text-white px-3 py-2 rounded-top d-flex justify-content-between align-items-center">
            <strong>${titleText}</strong>
            ${removeBtn}
        </div>
        <div class="p-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From (Departure City) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="segments[${index}][from_city]" 
                               id="seg_from_${index}" placeholder="e.g., New York (JFK)" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To (Arrival City) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="segments[${index}][to_city]" 
                               id="seg_to_${index}" placeholder="e.g., London (LHR)" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Departure Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="segments[${index}][departure_date]" 
                               id="seg_dep_${index}" required>
                    </div>
                </div>
                ${returnDateField}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Cabin Class <span class="text-danger">*</span></label>
                        <select class="form-control" name="segments[${index}][cabin_class]" required>
                            ${cabinOptions}
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Airline Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="segments[${index}][airline_name]" 
                               placeholder="e.g., British Airways" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Flight Number</label>
                        <input type="text" class="form-control" name="segments[${index}][flight_number]" 
                               placeholder="e.g., BA 178 (optional)">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Segment PNR <small class="text-muted">(optional override)</small></label>
                        <input type="text" class="form-control" name="segments[${index}][segment_pnr]" 
                               placeholder="Segment-specific PNR if different">
                    </div>
                </div>
            </div>
        </div>
    </div>`;
}

function removeSegment(index) {
    const block = document.getElementById(`segment_block_${index}`);
    if (block) {
        block.remove();
        segmentCount--;
        renumberSegments();
    }
}

function renumberSegments() {
    document.querySelectorAll('.segment-block').forEach((block, i) => {
        const title = block.querySelector('.text-white strong');
        if (title) {
            title.innerHTML = `<i class="fas fa-route mr-2"></i>Segment ${i + 1}`;
        }
    });
}

// ══════════════════════════════════════════════════════════════
// PAYMENT & MCO MANAGEMENT
// ══════════════════════════════════════════════════════════════

function recalcMCO() {
    const charged = parseFloat(document.getElementById('amount_charged').value) || 0;
    const airline = parseFloat(document.getElementById('amount_paid_airline').value) || 0;
    const suggested = (charged - airline).toFixed(2);

    const hint = document.getElementById('mco_hint');
    const mcoBox = document.getElementById('mco_suggested');
    const reqTotal = document.getElementById('required_total_display');

    if (charged > 0 || airline > 0) {
        hint.style.display = 'inline';
        mcoBox.textContent = '$' + suggested;
    } else {
        hint.style.display = 'none';
    }

    // Update split payment required total display
    if (reqTotal) reqTotal.textContent = '$' + charged.toFixed(2);

    // For full payment: auto-fill charge amount on card 1
    if (document.getElementById('payment_full').checked) {
        const firstCharge = document.querySelector('.charge-amount-input');
        if (firstCharge) firstCharge.value = charged.toFixed(2);
    }

    updateCardTotal();
}

function handlePaymentTypeChange() {
    const isSplit = document.getElementById('payment_split').checked;

    document.getElementById('split_payment_note').style.display = isSplit ? 'block' : 'none';
    document.getElementById('add_card_btn_wrapper').style.display = isSplit ? 'block' : 'none';
    document.getElementById('card_total_bar').style.display = isSplit ? 'block' : 'none';

    if (!isSplit) {
        // Remove all extra cards, keep only card 1
        removeAllExtraCards();
        // Auto-fill card 1 charge amount with full amount charged
        const charged = parseFloat(document.getElementById('amount_charged').value) || 0;
        const firstCharge = document.querySelector('.charge-amount-input');
        if (firstCharge) firstCharge.value = charged.toFixed(2);
    }

    updateCardTotal();
}

// ══════════════════════════════════════════════════════════════
// CARD MANAGEMENT
// ══════════════════════════════════════════════════════════════

function addCard() {
    const index = cardCount;
    const num = cardCount + 1;

    const months = buildMonthOptions();
    const years = buildYearOptions();

    // Get merchant options from blade (injected globally)
    const merchantOpts = window.merchantOptions || '<option value="">No merchants available</option>';

    const html = `
    <div class="card-item border rounded mb-3" id="card_block_${index}">
        <div class="bg-light px-3 py-2 d-flex justify-content-between align-items-center rounded-top">
            <strong><i class="fas fa-credit-card mr-2 text-success"></i>Card ${num}</strong>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCard(${index})">
                <i class="fas fa-trash"></i> Remove
            </button>
        </div>
        <div class="p-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Merchant / Payment Gateway <span class="text-danger">*</span></label>
                        <select name="cards[${index}][merchant_id]" class="form-control" required>
                            <option value="">-- Select Merchant --</option>
                            ${merchantOpts}
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Card Holder Name <span class="text-danger">*</span></label>
                        <input type="text" name="cards[${index}][card_holder_name]" 
                               class="form-control" required placeholder="As on card">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Card Number <span class="text-danger">*</span></label>
                        <input type="text" name="cards[${index}][card_number]" 
                               id="card_num_${index}" class="form-control card-number-input"
                               required placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Card Type <span class="text-danger">*</span></label>
                        <select name="cards[${index}][card_type]" id="card_type_${index}" class="form-control" required>
                            <option value="">Auto Detect</option>
                            <option value="VISA">VISA</option>
                            <option value="MASTERCARD">MASTERCARD</option>
                            <option value="AMEX">AMEX</option>
                            <option value="DISCOVER">DISCOVER</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Exp. Month <span class="text-danger">*</span></label>
                        <select name="cards[${index}][expiration_month]" class="form-control" required>
                            <option value="">MM</option>${months}
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Exp. Year <span class="text-danger">*</span></label>
                        <select name="cards[${index}][expiration_year]" class="form-control" required>
                            <option value="">YYYY</option>${years}
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>CVV <span class="text-danger">*</span></label>
                        <input type="password" name="cards[${index}][cvv]" 
                               class="form-control" required maxlength="4" placeholder="123" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Charge Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                            <input type="number" name="cards[${index}][charge_amount]" 
                                   class="form-control charge-amount-input"
                                   required step="0.01" min="0.01" placeholder="0.00"
                                   oninput="updateCardTotal()">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Billing Email <span class="text-danger">*</span></label>
                        <input type="email" name="cards[${index}][billing_email]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Billing Phone <span class="text-danger">*</span></label>
                        <input type="text" name="cards[${index}][billing_phone]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Billing Address <span class="text-danger">*</span></label>
                        <textarea name="cards[${index}][billing_address]" 
                                  class="form-control" rows="2" required 
                                  placeholder="Street, City, State, ZIP"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

    document.getElementById('cards_container').insertAdjacentHTML('beforeend', html);
    cardCount++;
    initCardNumberInput(index);
    updateCardTotal();
}

function removeCard(index) {
    const block = document.getElementById('card_block_' + index);
    if (block) block.remove();
    renumberCards();
    updateCardTotal();
}

function removeAllExtraCards() {
    document.querySelectorAll('.card-item').forEach((block, i) => {
        if (i > 0) block.remove();
    });
    cardCount = 1;
    renumberCards();
}

function renumberCards() {
    document.querySelectorAll('.card-item').forEach((block, i) => {
        const title = block.querySelector('strong');
        if (title) title.innerHTML = `<i class="fas fa-credit-card mr-2 text-success"></i>Card ${i + 1}`;
        const removeBtn = block.querySelector('.btn-outline-danger');
        if (removeBtn) removeBtn.style.display = i === 0 ? 'none' : 'inline-block';
    });
}

function updateCardTotal() {
    let total = 0;
    document.querySelectorAll('.charge-amount-input').forEach(inp => {
        total += parseFloat(inp.value) || 0;
    });

    const charged = parseFloat(document.getElementById('amount_charged').value) || 0;
    const totalEl = document.getElementById('card_total_display');
    const matchEl = document.getElementById('card_match_msg');

    if (!totalEl) return;

    totalEl.textContent = '$' + total.toFixed(2);

    if (Math.abs(total - charged) < 0.01) {
        totalEl.className = 'text-success font-weight-bold';
        matchEl.innerHTML = '<span class="badge badge-success"><i class="fas fa-check mr-1"></i>Card totals match the amount charged!</span>';
    } else {
        totalEl.className = 'text-danger font-weight-bold';
        const diff = charged - total;
        const msg = diff > 0
            ? `$${diff.toFixed(2)} still unallocated`
            : `$${Math.abs(diff).toFixed(2)} over-allocated`;
        matchEl.innerHTML = `<span class="badge badge-danger"><i class="fas fa-exclamation-triangle mr-1"></i>${msg}</span>`;
    }
}

function initCardNumberInput(index) {
    const input = document.getElementById('card_num_' + index);
    if (!input) return;
    input.addEventListener('input', function() {
        let v = this.value.replace(/\D/g, '').substring(0, 16);
        this.value = v.replace(/(.{4})/g, '$1 ').trim();
        const typeSelect = document.getElementById('card_type_' + index);
        if (!typeSelect) return;
        if (/^4/.test(v)) typeSelect.value = 'VISA';
        else if (/^5[1-5]/.test(v)) typeSelect.value = 'MASTERCARD';
        else if (/^3[47]/.test(v)) typeSelect.value = 'AMEX';
        else if (/^6(?:011|5)/.test(v)) typeSelect.value = 'DISCOVER';
        else typeSelect.value = '';
    });
}

function buildMonthOptions() {
    let html = '';
    for (let m = 1; m <= 12; m++) {
        const v = String(m).padStart(2, '0');
        html += `<option value="${v}">${v}</option>`;
    }
    return html;
}

function buildYearOptions() {
    let html = '';
    const current = new Date().getFullYear();
    for (let y = current; y <= current + 15; y++) {
        html += `<option value="${y}">${y}</option>`;
    }
    return html;
}

// ══════════════════════════════════════════════════════════════
// OPTIONAL SERVICES TOGGLE
// ══════════════════════════════════════════════════════════════

function toggleService(service) {
    const checkbox = document.getElementById(`${service}_required`);
    const section = document.getElementById(`${service}Section`);
    if (checkbox.checked) {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}

// ══════════════════════════════════════════════════════════════
// INITIALIZATION ON PAGE LOAD
// ══════════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', function() {


     // ── Flight type listener ──────────────────────────────────
 document.addEventListener('DOMContentLoaded', function () {

    // ── Use event delegation for flight type (works with @include partials) ──
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'flighttype') {
            handleFlightTypeChange();
        }
    });
    // ── Passengers ────────────────────────────────────────────
    updatePassengerUI();

    // ── Card #0 init ──────────────────────────────────────────
    initCardNumberInput(0);

    // ── Payment type listeners ────────────────────────────────
    document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
        radio.addEventListener('change', handlePaymentTypeChange);
    });

    // ── Amount listeners ──────────────────────────────────────
    document.getElementById('amount_charged').addEventListener('input', recalcMCO);
    document.getElementById('amount_paid_airline').addEventListener('input', recalcMCO);

    // ── Optional services already checked (validation fail restore) ──
    ['hotel', 'cab', 'insurance'].forEach(service => {
        const checkbox = document.getElementById(`${service}_required`);
        const section  = document.getElementById(`${service}Section`);
        if (checkbox && section && checkbox.checked) {
            section.style.display = 'block';
        }
    });

    // ── Restore flight type if old() value exists (validation fail) ──
    const flightTypeEl = document.getElementById('flighttype');
    if (flightTypeEl && flightTypeEl.value) {
        handleFlightTypeChange();
    }

    // ── MCO init ──────────────────────────────────────────────
    recalcMCO();
});
document.getElementById('bookingForm').addEventListener('submit', function (e) {
    const segments = document.querySelectorAll('.segment-block');
    if (segments.length === 0) {
        e.preventDefault();
        alert('Please select a Flight Type and fill in the flight segment details before submitting.');
        // Scroll to flight section
        document.getElementById('flighttype').scrollIntoView({ behavior: 'smooth' });
        return false;
    }
});

    // Generate initial passenger form
    updatePassengerUI();

    // Init card 0
    initCardNumberInput(0);

    // Check if services were previously selected (for validation errors)
    if (document.getElementById('hotel_required').checked) {
        document.getElementById('hotelSection').style.display = 'block';
    }
    if (document.getElementById('cab_required').checked) {
        document.getElementById('cabSection').style.display = 'block';
    }
    if (document.getElementById('insurance_required').checked) {
        document.getElementById('insuranceSection').style.display = 'block';
    }

    // Event listeners
    document.getElementById('amount_charged').addEventListener('input', recalcMCO);
    document.getElementById('amount_paid_airline').addEventListener('input', recalcMCO);
    
    document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
        radio.addEventListener('change', handlePaymentTypeChange);
    });

    document.getElementById('flighttype').addEventListener('change', handleFlightTypeChange);

    // Auto-calculate MCO on page load if values exist
    recalcMCO();


        // ── Form submit guard (prevent submit if no segments) ─────
    const form = document.getElementById('bookingForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            const segments = document.querySelectorAll('.segment-block');
            if (segments.length === 0) {
                e.preventDefault();
                alert('Please select a Flight Type and fill in the flight segment details before submitting.');
                const flightTypeEl = document.getElementById('flighttype');
                if (flightTypeEl) flightTypeEl.scrollIntoView({ behavior: 'smooth' });
                return false;
            }
        });
    }
});


