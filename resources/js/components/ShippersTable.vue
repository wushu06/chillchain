<template>
    <v-layout row class="">
        <v-flex xs12>
            <v-data-table
                v-if="load"
                item-key="name"
                class="elevation-1"
                loading
                loading-text="Loading... Please wait"
            ></v-data-table>
            <v-layout row class="mr-3 mt-3 mb-3 ml-3">
                <v-flex xs6>
                    <div>
                        <v-btn fab class="mb-3 mt-3 mr-2" depressed small @click="addShipper">
                            <v-icon color="grey">fas fa-plus</v-icon>
                        </v-btn>
                        <span>Add Shipper</span>
                    </div>
                </v-flex>
            </v-layout>
            <v-card ref="form" class="mb-5 mt-3">
                <v-card-text>
                    <v-text-field
                        ref="name"
                        @input="search"
                        v-model="name"
                        label="Search by Shipper name or address"
                        required
                    ></v-text-field>
                </v-card-text>
            </v-card>
            <div class="total" v-if="total">
                <span><strong>Total:</strong> {{total}}</span>
            </div>
            <div class="total" v-if="total === 0">
                <h5>No result!</h5>
            </div>
            <v-simple-table v-if="total">
                <template v-slot:default>
                    <thead>
                    <tr>
                        <th class="text-left"  style="min-width: 200px">
                            Action
                        </th>
                        <th class="text-left">
                            Name
                        </th>
                        <th class="text-left">
                            Address
                        </th>
                        <th class="text-left">
                            Primary Contact
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="item in shippers"
                        :key="item.id"
                    >
                        <td>
                            <v-btn fab class="transparent-color mr-3" depressed  small @click="edit(item)">
                                <v-icon color="grey">fas fa-edit</v-icon>
                            </v-btn>
                            <v-btn fab class="transparent-color mr-3" depressed  small @click="view(item)">
                                <v-icon color="grey">fas fa-eye</v-icon>
                            </v-btn>
                            <v-btn fab class="transparent-color" depressed  small @click="deleteShipper(item.id)">
                                <v-icon color="pink">fas fa-trash</v-icon>
                            </v-btn>
                        </td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.address }}</td>
                        <td><p v-for="primary in item.primary_contact" :key="primary.id"><span>{{primary.name}}</span></p></td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>

            <div class="text-xs-center pagination-wrapper">
                <v-pagination
                    v-if="pageNumber > increment"
                    v-model="page"
                    :length="Math.ceil(pageNumber / increment)"
                    @input="next"
                    :total-visible="5"
                    color="black"
                    class="my-4"
                ></v-pagination>
                <div class="total" v-if="total">
                    <span><strong>Total:</strong> {{total}}</span>
                </div>
            </div>
            <v-dialog
                fullscreen hide-overlay transition="dialog-bottom-transition"
                v-model="dialogEdit"
                max-width="900">
                <v-card>
                    <v-toolbar dark color="primary">
                        <v-btn icon dark @click="dialogEdit = false">
                            <v-icon color="red">fas fa-times</v-icon>
                        </v-btn>
                        <v-toolbar-title>Edit:</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-list>
                        <Form :contacts="contacts" :shipper="shipper" @clicked="onShipperUpdated" :key="componentKey"/>
                    </v-list>

                </v-card>
            </v-dialog>
            <v-dialog
                fullscreen hide-overlay transition="dialog-bottom-transition"
                v-model="dialogView"
                max-width="900">
                <v-card>
                    <v-toolbar dark color="primary">
                        <v-btn icon dark @click="dialogView = false">
                            <v-icon color="red">fas fa-times</v-icon>
                        </v-btn>
                        <v-toolbar-title>View:</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-list>
                        <Contact :shipper="shipper" :key="componentKey"/>
                    </v-list>

                </v-card>
            </v-dialog>
            <v-dialog
                v-model="dialog"
                max-width="290">
                <v-card>
                    <v-card-title class="headline">Deleting:</v-card-title>
                    <v-card-text>
                        Are you sure you want to delete this Shipper?
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="green darken-1"
                            text="text"
                            @click="dialog = false"
                        >
                            Cancel
                        </v-btn>
                        <v-btn
                            color="pink"
                            text="text"
                            @click="deleteConfirmed"
                        >
                            Delete
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog
                fullscreen hide-overlay transition="dialog-bottom-transition"
                v-model="dialogAddShipper"
                max-width="900"
            >
                <v-card>
                    <v-toolbar dark color="primary">
                        <v-btn icon dark @click="dialogAddShipper = false">
                            <v-icon color="red">fas fa-times</v-icon>
                        </v-btn>
                        <v-toolbar-title>New Shipper</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-list>
                        <Form :contacts="contacts" :key="componentKey" @clicked="onShipperAdded"/>
                    </v-list>
                </v-card>
            </v-dialog>
            <v-snackbar
                v-model="snackbar"
                bottom="bottom"
                right="right"
                :timeout="timeout">
                {{message}}
                <v-btn
                    color="pink"
                    text
                    @click="snackbar = false"
                >
                    Close
                </v-btn>
            </v-snackbar>
        </v-flex>
    </v-layout>
</template>

<script>


    import axios from "axios";
    import Form from "./Form";
    import Contact from "./Contact";

    export default {
        props: [],
        data: () => ({
            name: null,
            id: null,
            shipper: null,
            shippers: {},
            timeout: 6000,
            snackbar: false,
            message: '',
            total: 0,
            errors: '',
            dialog: false,
            dialogEdit: false,
            dialogView: false,
            dialogAddShipper: false,
            page: 1,
            pageNumber: 0,
            currentPage: 0,
            increment: 10,
            option: null,
            load: true,
            componentKey: 0,
            contacts: []
        }),
        components: {
            Form, Contact
        },
        methods: {
            next(page) {
                this.page = page;
                if (this.name && this.name !== '') {
                    this.search();
                } else {
                    this.getData();
                }
            },
            search() {
                if (this.name === '') {
                    this.getData();
                    return;
                }
                this.getData(`/api/shippers/search/${this.name}`);
            },
            edit(shipper) {
                this.dialogEdit = true;
                this.shipper = shipper;
                this.componentKey += 1;
            },
            view(shipper) {
                this.dialogView = true;
                this.shipper = shipper;
                this.componentKey += 1;
            },
            onShipperUpdated(newData) {
                let self = this;
                axios.put(
                    `/api/shippers/${this.shipper.id}`,
                    newData
                )
                    .then(res => {
                        self.setData(res);
                        self.dialogEdit = false;
                        self.snackbar = true;
                        self.message = `${self.shipper.name}  has been updated!`;
                        this.getData()
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            deleteConfirmed() {
                this.dialog = false;
                if (!this.id) {
                    return;
                }
                let self = this;
                axios.delete(
                    `/api/shippers/${this.id}`,
                )
                    .then(res => {
                        self.setData(res);
                        self.snackbar = true;
                        self.message = 'Shipper has been deleted!';
                        this.getData()
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            addShipper(){
                this.dialogAddShipper = true
            },
            onShipperAdded(data){
                let self = this;
                axios.post(
                    '/api/shippers',
                    data
                )
                    .then(res => {
                        self.setData(res);
                        self.dialogAddShipper = false;
                        self.snackbar = true;
                        self.message = `${data.name}  has been added!`;
                        this.getData()
                    })
                    .catch(error => {
                        console.log(error);
                    })

            },
            deleteShipper(id) {
                this.dialog = true;
                this.id = id
            },
            setData(res) {
                this.pageNumber = res.data.total;
                this.total = res.data.total;
                this.shippers = res.data.data;
                this.load = false;
            },
            getData(url = '/api/shippers', concat = '?') {
                let self = this;
                let page = `${concat}page=${this.page}&dir=${this.dir}`;
                axios.get(`${url}${page}`)
                    .then((res) => {
                        self.setData(res)
                    })
                    .catch(error => {
                        console.log(error);
                    })

                axios.get('api/contacts/')
                    .then((res) => {
                        self.contacts = res.data
                    })
                    .catch(error => {
                        console.log(error);
                    })
            }
        },
        mounted() {
            this.getData();
        }
    }
</script>

