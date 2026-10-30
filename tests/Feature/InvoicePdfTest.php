<?php

it('serves real invoice text and the total through HTTP', function () {
    $response = $this->get('/invoices/1001/pdf');

    $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
    expect($response)
        ->toHavePdfText('Invoice 1001')
        ->toHavePdfText('Acme Ltd')
        ->toHavePdfText('Consulting')
        ->toHavePdfText('Total: GBP 120.00');
});

it('keeps the invoice to one PDF page', function () {
    expect($this->get('/invoices/1001/pdf'))->toHavePdfPageCount(1);
});

it('matches the reviewed invoice rendering', function () {
    expect($this->get('/invoices/1001/pdf'))
        ->toMatchPdfSnapshot('invoice-1001');
})->group('visual')->skip(
    getenv('PDF_DEMO_VISUAL') !== '1',
    'Opt in with composer test:visual; see README for explicit baseline generation.',
);

it('does not provide a landing page or other invoices', function () {
    $this->get('/')->assertNotFound();
    $this->get('/invoices/1002/pdf')->assertNotFound();
    $this->put('/storage/demo.pdf')->assertNotFound();
});
