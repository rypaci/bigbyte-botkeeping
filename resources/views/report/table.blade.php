    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Date</th><th> COA </th><th> Customer/Payee/Supplier </th><th> Ref </th>
                    <th> Memo </th><th> Debit </th><th> Credit </th><th> Balance </th>
                </tr>
            </thead>
            <tbody>
            {{-- */$total_debit = $total_credit = 0;/* --}}
            @foreach($vouchers as $v)
                {{-- */$total_debit += $v->debit; $total_credit += $v->credit;/* --}}
                <tr>
                    <td>{{ $v->date }}</td><td>{{ $v->coa_code . ' -- ' . $v->coa_name }}</td><td>{{ $v->entity_name }}</td><td>{{ $v->ref_number }}</td>
                    <td>{{ $v->description }}</td>
                    <td class="text-right">{{ $v->debit_formatted }}</td>
                    <td class="text-right">{{ $v->credit_formatted }}</td>
                    <td class="text-right @if(0>$v->balance){{'text-red'}}@endif">{{ $v->balance_formatted }}</td>
                </tr>
            @endforeach
                <tr class="total-row">
                  <td colspan="4">  </td>
                  <td class="text-right"> Total: </td>
                  <td class="text-right debit">{{ number_format( $total_debit, 2, '.', ',' ) }}</td>
                  <td class="text-right credit">{{ number_format( $total_credit, 2, '.', ',' ) }}</td>
                  <td>  </td>
                </tr>
            </tbody>
        </table>
    </div>