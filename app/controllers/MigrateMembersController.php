<?php

ini_set('memory_limit','3072M');

class MigrateMembersController extends BaseController {

    // public function __construct()
    // {
    //     DB::setDefaultConnection('pcms_migrate');
    // }

    public function getImportMembers()
    {
        $number = Input::get('file', 1);
        $pathFile = "./20140515-excel-migrate-itruemart/member_{$number}.xlsx";
        $objPHPExcel = PHPExcel_IOFactory::load($pathFile);

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        unset($sheetData[1]);

        foreach ($sheetData as $v)
        {
            if (Validator::make(array('thai_id' => $v['C']), array('thai_id' => 'digits:13'))->passes())
            {
                $thai_id = $v['C'];
            }
            else
            {
                $thai_id = null;
            }

            if (Validator::make(array('email' => $v['H']), array('email' => 'email'))->passes())
            {
                $email = $v['H'];
            }
            else
            {
                $email = null;
            }

            // There is no one who enter their number, so I use column email to validate again
            if (Validator::make(array('phone' => $v['H']), array('phone' => 'digits:10'))->passes())
            {
                $phone = $v['H'];
            }
            else
            {
                $phone = null;
            }

            if (Validator::make(array('trueyou' => $v['J']), array('trueyou' => 'in:red,black'))->passes())
            {
                $trueyou = $v['J'];
            }
            else
            {
                $trueyou = null;
            }

            $member = MigratedMember::where('sso_id', $v['B'])->first();

            $exists = ! empty($member);

            if ( ! $exists)
            {
                $member = new MigratedMember;
            }

            $member->app_id = 1; // iTruemart is 1
            $member->sso_id = $v['B'];
            $member->thai_id = $thai_id;
            $member->subscribe = (int) (boolean) $v['G'];
            $member->email = $email;
            $member->phone = $phone;
            $member->trueyou = $trueyou;
            $member->login_at = $v['K'];
            #$member->created_at = $v['L'];
            #$member->updated_at = $v['M'];
            #$member->deleted_at = null;

            $dirty = $member->getDirty();

            if ( ! empty($dirty)) // some columns changed
            {
                $member->migrate_status = 'no';
            }

            $member->save();

            if ( ! $exists)
            {
                $memberMapping = new MigratedMemberMapping;

                $memberMapping->itm_member_id = $v['A'];
                $memberMapping->itm_sso_id = $v['B'];
                $memberMapping->pcms_member_id = $member->getKey();

                $memberMapping->save();
            }
        }

        echo "Import Category Data from Excel - Complete";
        echo "<br> Path file - {$pathFile}";
    }

    public function getMigrateMembers()
    {
        $members = MigratedMember::where('migrate_status', 'no')->get();

        $members->each(function($member) {

            try
            {
                Member::insert(array_only($member->toArray(), array('app_id', 'sso_id', 'thai_id', 'subscribe', 'email', 'phone', 'trueyou', 'login_at', 'created_at', 'updated_at')));
            }
            catch (Exception $e)
            {
                echo $e->getMessage();
            }

            $member->migrate_status = 'yes';
            $member->save();

        });
    }

    function getImportMemberAddresses()
    {
        $pathFile = "./20140515-excel-migrate-itruemart/member_address.xlsx";
        $objPHPExcel = PHPExcel_IOFactory::load($pathFile);

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        unset($sheetData[1]);

        foreach ($sheetData as $v)
        {
            if (Validator::make(array('name' => $v['B']), array('name' => 'required'))->passes())
            {
                $name = $v['B'];
            }
            else
            {
                $name = null;
            }

            if (Validator::make(array('email' => $v['C']), array('email' => 'email'))->passes())
            {
                $email = $v['C'];
            }
            else
            {
                $email = null;
            }

            $customerAddress = MigratedCustomerAddress::where('customer_ref_id', $v['A'])->first();

            if (empty($customerAddress))
            {
                $customerAddress = new MigratedCustomerAddress;
            }

            $customerAddress->app_id = 1; // iTruemart is 1
            $customerAddress->customer_ref_id = $v['A'];
            $customerAddress->name = $name;
            $customerAddress->email = $email;
            $customerAddress->province_id = $v['E'];
            $customerAddress->city_id = $v['F'];
            $customerAddress->district_id = $v['G'];
            $customerAddress->address = $v['H'];
            $customerAddress->postcode = substr($v['I'], 0, 5);
            $customerAddress->phone = $v['J'];
            #$customerAddress->created_at = $v['K'];
            #$customerAddress->updated_at = $v['L'];
            #$customerAddress->deleted_at = null;

            $dirty = $customerAddress->getDirty();

            if ( ! empty($dirty)) // some columns changed
            {
                $customerAddress->migrate_status = 'no';
            }

            $customerAddress->save();
        }

        echo "Import Category Data from Excel - Complete";
        echo "<br> Path file - {$pathFile}";
    }

    function getMigrateMemberAddresses()
    {
        $customerAddress = MigratedCustomerAddress::where('migrate_status', 'no')->get();

        $customerAddress->each(function($address) {

            try
            {
                CustomerAddress::insert(array_only($address->toArray(), array('app_id', 'customer_ref_id', 'name', 'email', 'province_id', 'city_id', 'district_id', 'address', 'postcode', 'phone', 'created_at', 'updated_at')));
            }
            catch (Exception $e)
            {
                echo $e->getMessage();
            }

            $address->migrate_status = 'yes';
            $address->save();

        });
    }

}