{{-- ============================================================ --}}
{{-- SECTION 3: FLIGHT DETAILS (Dynamic by flight type)          --}}
{{-- ============================================================ --}}
<div class="card mt-4">
    <div class="card-header bg-success text-white">
        <h3 class="card-title">
            <i class="fas fa-plane mr-2"></i>Flight Details
        </h3>
    </div>
    <div class="card-body">

        @error('pnr')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        @error('segments')
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Please select a <strong>Flight Type</strong> and fill at least one flight segment.
            </div>
        @enderror

        {{-- ROW 1: Flight Type | GK PNR | Airline PNR --}}
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Flight Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="flighttype" name="flight_type" required>
                        <option value="">-- Select Type --</option>
                        <option value="oneway"    {{ old('flight_type') == 'oneway'    ? 'selected' : '' }}>One Way</option>
                        <option value="roundtrip" {{ old('flight_type') == 'roundtrip' ? 'selected' : '' }}>Round Trip</option>
                        <option value="multicity" {{ old('flight_type') == 'multicity' ? 'selected' : '' }}>Multi City</option>
                    </select>
                </div>
            </div>
            {{-- temporary code just for testing  --}}
            <script>
                console.log('Flight type el found:', document.getElementById('flighttype'));
            </script>
            <div class="col-md-4">
                <div class="form-group">
                    <label>GK PNR</label>
                    <input type="text" class="form-control" name="gk_pnr"
                           value="{{ old('gk_pnr') }}" placeholder="Temp PNR from GDS">
                    <small class="text-muted">At least one PNR (GK or Airline) required</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Airline PNR</label>
                    <input type="text" class="form-control" name="airline_pnr"
                           value="{{ old('airline_pnr') }}" placeholder="Actual PNR from airline">
                    <small class="text-muted">At least one PNR (GK or Airline) required</small>
                </div>
            </div>
        </div>

        {{-- Flight type hint (shown before type is selected) --}}
        <div id="flighttypehint" class="alert alert-info py-2">
            <i class="fas fa-info-circle mr-1"></i>
            Please select a <strong>Flight Type</strong> above to continue filling flight details.
        </div>

        {{-- Segments Container (hidden until flight type selected) --}}
        <div id="segmentscontainer" style="display:none;">
            {{-- Segment rows injected here by JS --}}
        </div>

        {{-- Add Flight button (Multi City only) --}}
        <div id="addsegmentwrapper" style="display:none;" class="mt-2">
            <button type="button" class="btn btn-outline-success btn-sm" onclick="addSegment()">
                <i class="fas fa-plus mr-1"></i> Add Flight Segment
            </button>
            <small class="text-muted ml-2">You can add up to 6 segments for multi-city itineraries</small>
        </div>

    </div>
</div>
