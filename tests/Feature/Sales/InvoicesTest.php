<?php

namespace Tests\Feature\Sales;

use App\Jobs\Document\CreateDocument;
use App\Models\Document\Document;
use Tests\Feature\FeatureTestCase;

class InvoicesTest extends FeatureTestCase
{
    public function testItShouldSeeInvoiceListPage()
    {
        $this->loginAs()
            ->get(route('invoices.index'))
            ->assertStatus(200)
            ->assertSeeText(trans_choice('general.invoices', 2));
    }

    public function testItShouldSeeInvoiceCreatePage()
    {
        $this->loginAs()
            ->get(route('invoices.create'))
            ->assertStatus(200)
            ->assertSeeText(trans('general.title.new', ['type' => trans_choice('general.invoices', 1)]));
    }

    public function testItShouldCreateInvoice()
    {
        $request = $this->getRequest();

        $this->loginAs()
            ->post(route('invoices.store'), $request)
            ->assertStatus(200);

        $this->assertFlashLevel('success');

        $this->assertDatabaseHas('documents', [
            'document_number' => $request['document_number'],
        ]);
    }

    public function testItShouldCreateInvoiceWithRecurring()
    {
        $request = $this->getRequest(true);

        $this->loginAs()
            ->post(route('invoices.store'), $request)
            ->assertStatus(200);

        $this->assertFlashLevel('success');

        $this->assertDatabaseHas('documents', [
            'document_number' => $request['document_number'],
        ]);
    }

    public function testItShouldSeeInvoiceUpdatePage()
    {
        $request = $this->getRequest();

        $invoice = $this->dispatch(new CreateDocument($request));

        $this->loginAs()
            ->get(route('invoices.edit', $invoice->id))
            ->assertStatus(200)
            ->assertSee($invoice->contact_email);
    }

    public function testItShouldUpdateInvoice()
    {
        $request = $this->getRequest();

        $invoice = $this->dispatch(new CreateDocument($request));

        $request['contact_email'] = $this->faker->safeEmail;

        $this->loginAs()
            ->patch(route('invoices.update', $invoice->id), $request)
            ->assertStatus(200)
			->assertSee($request['contact_email']);

        $this->assertFlashLevel('success');

        $this->assertDatabaseHas('documents', [
            'document_number' => $request['document_number'],
            'contact_email' => $request['contact_email'],
        ]);
    }

    public function testItShouldDeleteInvoice()
    {
        $request = $this->getRequest();

        $invoice = $this->dispatch(new CreateDocument($request));

        $this->loginAs()
            ->delete(route('invoices.destroy', $invoice->id))
            ->assertStatus(200);

        $this->assertFlashLevel('success');

        $this->assertSoftDeleted('documents', [
            'document_number' => $request['document_number'],
        ]);
    }

    public function getRequest($recurring = false)
    {
        $factory = Document::factory();

        $factory = $recurring ? $factory->invoice()->items()->recurring() : $factory->invoice()->items();

        return $factory->raw();
    }
}
