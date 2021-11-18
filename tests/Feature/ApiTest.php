<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Shipper;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use DatabaseMigrations;

    /** @test * */
    public function api_can_show_all_shippers()
    {
        $fakeData = $this->getFakeShipperData();
        Shipper::factory()->create($fakeData);
        $response = $this->get('/api/shippers');
        $response->assertJsonFragment(['name' => $fakeData['name']]);

    }

    protected function getFakeShipperData(): array
    {
        $faker = Factory::create();
        return [
            'name' => $faker->name(),
            'address' => $faker->address(),
        ];
    }

    /** @test * */
    public function api_can_add_shipper()
    {
        $fakeData = $this->getFakeShipperData();
        $shipper = Shipper::factory()->make();
        $this->post('/api/shippers', $fakeData);
        $shipper->refresh();
        $this->assertDatabaseHas('shippers', ['name' => $fakeData['name']]);

    }

    /** @test * */
    public function api_can_update_shipper()
    {
        $fakeData = $this->getFakeShipperData();
        $shipper = Shipper::factory()->create($fakeData);
        $this->put('/api/shippers/' . $shipper->id, ['name' => 'MyShipperUpdated', 'address' => 'new address']);
        $shipper->refresh();
        $this->assertDatabaseHas('shippers', ['name' => 'MyShipperUpdated']);
    }

    /** @test * */
    public function api_can_delete_shipper()
    {
        $fakeData = $this->getFakeShipperData();
        $fakeData2 = $this->getFakeShipperData();
        $shipper = Shipper::factory()->create($fakeData);
        $shipper2 = Shipper::factory()->create($fakeData2);
        $id = $shipper->id;
        $this->delete('/api/shippers/' . $id, ['id' => $id]);
        $this->assertDatabaseMissing('shippers', ['id' => $id]);
        $this->assertDatabaseHas('shippers', ['id' => $shipper2->id]);

    }

    /** @test * */
    public function api_can_add_contacts_to_shipper()
    {
        $fakeData = $this->getFakeShipperData();
        Shipper::factory()->make();
        $contacts = Contact::factory(2)->create();
        $fakeData['contacts'] = $contacts->pluck('id');
        $fakeData['primary_contact'] = $contacts->first()->id;
        $this->post('/api/shippers', $fakeData);
        $shipper = Shipper::latest()->first();
        $this->assertDatabaseHas('contacts', ['is_primary' => 1, 'name' => $contacts->first()->name, 'shipper_id' => $shipper->id]);
    }

    /** @test * */
    public function api_can_update_contacts_in_shipper()
    {
        $fakeData = $this->getFakeShipperData();
        $shipper = Shipper::factory()->create($fakeData);
        Contact::factory(2)->create(['shipper_id' => $shipper->id]);
        $newContact = Contact::factory()->create();
        $this->put('/api/shippers/' . $shipper->id, ['name' => 'MyShipperUpdated', 'address' => 'new address', 'contacts' => [$newContact->id]]);
        $this->assertDatabaseHas('contacts', ['name' => $newContact->name, 'shipper_id' => $shipper->id]);
    }

    /** @test * */
    public function api_can_update_primary_contact_in_shipper()
    {
        $fakeData = $this->getFakeShipperData();
        $shipper = Shipper::factory()->create($fakeData);
        $firstContact = Contact::factory()->create(['shipper_id' => $shipper->id, 'is_primary' => 1]);
        $secondContact = Contact::factory()->create(['shipper_id' => $shipper->id]);
        $this->put('/api/shippers/' . $shipper->id, ['name' => 'MyShipperUpdated', 'address' => 'new address', 'primary_contact' => $secondContact->id]);
        $this->assertDatabaseHas('contacts', ['id' => $secondContact->id, 'is_primary' => 1]);
        $this->assertDatabaseHas('contacts', ['id' => $firstContact->id, 'is_primary' => 0]);
    }
}
