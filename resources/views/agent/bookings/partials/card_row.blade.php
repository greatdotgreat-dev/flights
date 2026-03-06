<?php
// resources/views/agent/bookings/partials/card_row.blade.php
// Included inline via @include or JS template
// $index and $merchants must be passed
?>
<div class="row">
    {{-- Merchant --}}
    <div class="col-md-4">
        <div class="form-group">
            <label>Merchant / Payment Gateway <span class="text-danger">*</span></label>
            <select name="cards[{{ $index }}][merchant_id]" class="form-control merchant-select" required>
                <option value="">-- Select Merchant --</option>
                @foreach($merchants as $merchant)
                    <option value="{{ $merchant->id }}"
                        {{ old("cards.{$index}.merchant_id") == $merchant->id ? 'selected' : '' }}>
                        {{ $merchant->name }}
                        @if($merchant->code) ({{ $merchant->code }}) @endif
                        — {{ $merchant->currency }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Card Holder Name --}}
    <div class="col-md-4">
        <div class="form-group">
            <label>Card Holder Name <span class="text-danger">*</span></label>
            <input type="text" name="cards[{{ $index }}][card_holder_name]"
                   class="form-control" required
                   value="{{ old("cards.{$index}.card_holder_name") }}"
                   placeholder="As on card">
        </div>
    </div>

    {{-- Card Number --}}
    <div class="col-md-4">
        <div class="form-group">
            <label>Card Number <span class="text-danger">*</span></label>
            <input type="text" name="cards[{{ $index }}][card_number]"
                   class="form-control card-number-input" required
                   value="{{ old("cards.{$index}.card_number") }}"
                   placeholder="1234 5678 9012 3456"
                   maxlength="19" autocomplete="off">
        </div>
    </div>
</div>

<div class="row">
    {{-- Card Type --}}
    <div class="col-md-3">
        <div class="form-group">
            <label>Card Type <span class="text-danger">*</span></label>
            <select name="cards[{{ $index }}][card_type]" class="form-control card-type-select" required>
                <option value="">Auto Detect</option>
                <option value="VISA"       {{ old("cards.{$index}.card_type")=='VISA'?'selected':'' }}>VISA</option>
                <option value="MASTERCARD" {{ old("cards.{$index}.card_type")=='MASTERCARD'?'selected':'' }}>MASTERCARD</option>
                <option value="AMEX"       {{ old("cards.{$index}.card_type")=='AMEX'?'selected':'' }}>AMEX</option>
                <option value="DISCOVER"   {{ old("cards.{$index}.card_type")=='DISCOVER'?'selected':'' }}>DISCOVER</option>
            </select>
        </div>
    </div>

    {{-- Exp Month --}}
    <div class="col-md-2">
        <div class="form-group">
            <label>Exp. Month <span class="text-danger">*</span></label>
            <select name="cards[{{ $index }}][expiration_month]" class="form-control" required>
                <option value="">MM</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}"
                        {{ old("cards.{$index}.expiration_month") == str_pad($m,2,'0',STR_PAD_LEFT) ? 'selected' : '' }}>
                        {{ str_pad($m,2,'0',STR_PAD_LEFT) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Exp Year --}}
    <div class="col-md-2">
        <div class="form-group">
            <label>Exp. Year <span class="text-danger">*</span></label>
            <select name="cards[{{ $index }}][expiration_year]" class="form-control" required>
                <option value="">YYYY</option>
                @foreach(range(date('Y'), date('Y')+15) as $y)
                    <option value="{{ $y }}" {{ old("cards.{$index}.expiration_year") == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- CVV --}}
    <div class="col-md-2">
        <div class="form-group">
            <label>CVV <span class="text-danger">*</span></label>
            <input type="password" name="cards[{{ $index }}][cvv]"
                   class="form-control" required
                   maxlength="4" placeholder="123" autocomplete="off">
        </div>
    </div>

    {{-- Charge Amount --}}
    <div class="col-md-3">
        <div class="form-group">
            <label>Charge Amount <span class="text-danger">*</span></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                <input type="number" name="cards[{{ $index }}][charge_amount]"
                       class="form-control charge-amount-input" required
                       step="0.01" min="0.01"
                       value="{{ old("cards.{$index}.charge_amount") }}"
                       placeholder="0.00"
                       oninput="updateCardTotal()">
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Billing Email --}}
    <div class="col-md-4">
        <div class="form-group">
            <label>Billing Email <span class="text-danger">*</span></label>
            <input type="email" name="cards[{{ $index }}][billing_email]"
                   class="form-control" required
                   value="{{ old("cards.{$index}.billing_email") }}">
        </div>
    </div>

    {{-- Billing Phone --}}
    <div class="col-md-4">
        <div class="form-group">
            <label>Billing Phone <span class="text-danger">*</span></label>
            <input type="text" name="cards[{{ $index }}][billing_phone]"
                   class="form-control" required
                   value="{{ old("cards.{$index}.billing_phone") }}">
        </div>
    </div>

    {{-- Billing Address --}}
    <div class="col-md-4">
        <div class="form-group">
            <label>Billing Address <span class="text-danger">*</span></label>
            <textarea name="cards[{{ $index }}][billing_address]"
                      class="form-control" rows="2" required
                      placeholder="Street, City, State, ZIP">{{ old("cards.{$index}.billing_address") }}</textarea>
        </div>
    </div>
</div>
