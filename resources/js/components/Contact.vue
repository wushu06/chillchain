<template>
    <div>
        <ul
            v-for="item in contacts"
               :key="item.id"
        >
            <li>
                <h4>{{ item.name }} {{ item.is_primary ? '(Primary Contact)' : '' }}</h4>
                <p>{{ item.number }}</p>
            </li>
        </ul>
    </div>
</template>
<script>
import axios from "axios";

export default {
    props: [
        'shipper',
    ],
    data: () => ({
        contacts: {}
    }),
    beforeMount() {

        if(this.shipper !== undefined) {
            let self = this;
            axios.get(`api/shippers/contacts/${this.shipper.id}`)
                .then((res) => {
                    self.contacts = res.data
                })
                .catch(error => {
                    console.log(error);
                })
        }
    }

}
</script>
