<template>
    <div class="text-xs-center">
        <div class="custom_pagination">
                 <span class="custom_pagination_item"   v-for="num in  Math.ceil(pageNumber / increment)">
                    <span :class="` ${ num / page === 1 ? 'active' : 'no'}`" @click="next(num)">{{num}}</span>
                </span>
        </div>
    </div>
</template>
<script>
    export default {
        props: [
            'pageNumber'
        ],
        data: () => ({
            currentPage: 0,
            page: 1,
            increment: 3
        }),
        methods: {
            next(num){
                this.currentPage = num === 1 ? 0 : (num-1) * 3
                this.page = num
                this.$emit('clicked', {currentPage:this.currentPage, increment: this.increment});
            }
        }
    };
</script>
