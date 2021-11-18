<template>
    <v-container class="pa-0">
        <form>
            <v-container>
                <v-layout row wrap>
                    <v-flex
                        xs12
                        md12>
                        <v-text-field
                            v-model="name"
                            :label="`Name: ${shipper !== undefined && shipper.name ? shipper.name : '' }`">
                        </v-text-field>
                        <v-textarea
                            v-model="address"
                            :label="`Address: ${shipper !== undefined && shipper.address ? shipper.address : ''}`">
                        </v-textarea>
                        <v-col
                            cols="12"
                            sm="6"
                        >
                            <v-select
                                v-model="contactsValue"
                                :items="contactsList"
                                item-text="name"
                                chips
                                clearable
                                return-object
                                label="Contacts"
                                multiple
                            ></v-select>
                            <ul
                                v-for="item in contactsValue"
                                :key="item.id"
                            >
                                <li>
                                    <p>{{ item.name }}</p>
                                </li>
                            </ul>
                        </v-col>
                        <v-col
                            cols="12"
                            sm="6"
                        >
                            <v-select
                                :items="contactsList"
                                v-model="primary_contact"
                                item-text="name"
                                return-object
                                label="Primary Contact "
                            ></v-select>
                            <div
                                v-for="item in primaryContact"
                                :key="item.id"
                            >
                                <p>{{ item.name }}</p>
                            </div>
                        </v-col>
                        <v-btn color="primary" @click="submit">{{ shipper !== undefined ? 'Update' : 'Add' }}</v-btn>
                    </v-flex>
                </v-layout>
            </v-container>
        </form>
    </v-container>
</template>
<script>

export default {
    props: [
        'shipper', 'contacts'
    ],
    data: () => ({
        name: '',
        address: '',
        selected: [],
        contactsValue: [],
        contactsList: [],
        primaryContact: [],
        primary_contact: ''
    }),
    methods: {
        submit() {
            let contacts = this.contactsValue.map(value => {
                return value.id
            })
            let data = {
                name: this.name,
                address: this.address,
                contacts: contacts,
                primary_contact: this.primary_contact ? this.primary_contact.id : null
            };
            this.$emit('clicked', data);
        },

    },
    beforeMount() {
        if (this.shipper !== undefined) {
            this.name = this.shipper.name
            this.address = this.shipper.address
            this.contactsValue = this.shipper.contacts
            this.primaryContact = this.shipper.primary_contact
            this.contactsList = this.contacts.all_contacts
        }else{
            this.contactsList = this.contacts.contacts
        }

    }

}
</script>
