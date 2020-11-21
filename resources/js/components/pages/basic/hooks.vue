<template>
    <div>
        <h1 ref="myref">Hooks</h1>
        <h2>Name : {{name}}</h2>
    </div>
</template>

<script>
export default {
    data(){
        return{
            name: 'hello'
        }
    },
    beforeCreate(){
        console.log(this.name)
    },
    created(){
        console.log('???????????', this.name)
    },
    mounted(){
        console.log(this.$refs.myref)
    }
}
</script>