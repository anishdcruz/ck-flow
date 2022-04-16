<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(OrganizationsTableSeeder::class);
        $this->call(ContactsTableSeeder::class);
        // $this->call(NotesTableSeeder::class);
        $this->call(ItemsTableSeeder::class);
        $this->call(LeadsTableSeeder::class);
        $this->call(OpportunitiesTableSeeder::class);
        // $this->call(ImagesTableSeeder::class);
        // $this->call(TemplatesTableSeeder::class);
        $this->call(ProposalsTableSeeder::class);
        $this->call(ContractsTableSeeder::class);
        $this->call(ProjectsTableSeeder::class);
        // $this->call(PhoneCallsTableSeeder::class);
        // $this->call(ActivitiesTableSeeder::class);
        $this->call(InvoicesTableSeeder::class);
        $this->call(PaymentsTableSeeder::class);
        $this->call(VendorsTableSeeder::class);
        $this->call(ExpensesTableSeeder::class);
        // $this->call(CustomFieldsTableSeeder::class);

        $this->call(UserMetricsTableSeeder::class);
    }
}
