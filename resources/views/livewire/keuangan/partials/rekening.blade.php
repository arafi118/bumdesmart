<div class="col-sm-6">
    <div class="my-2">
        <label class="form-label" for="sumber_dana">{{ $label1 }}</label>
        <select class="form-control tom-select" name="sumber_dana" id="sumber_dana">
            <option value="">-- {{ $label1 }} --</option>
            @foreach($rek1 as $r1)
                <option value="{{ $r1->kode }}">
                    {{ $r1->kode }}. {{ $r1->nama }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="col-sm-6">
    <div class="my-2">
        <label class="form-label" for="disimpan_ke">{{ $label2 }}</label>
        <select class="form-control tom-select" name="disimpan_ke" id="disimpan_ke">
            <option value="">-- {{ $label2 }} --</option>
            @foreach($rek2 as $r2)
                <option value="{{ $r2->kode }}">
                    {{ $r2->kode }}. {{ $r2->nama }}
                </option>
            @endforeach
        </select>
    </div>
</div>
