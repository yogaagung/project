<template>
    <div>
        <div class="content">
			<div class="container-fluid">
                <h1>Use Com A,B,C</h1>
                <h2>Current state : {{getCounter}}</h2> 
                <div>
                    <comA></comA>
                </div>
                <div>
                    <comB></comB>
                </div>
                <div>
                    <comC></comC>
                </div>
                <Button type="info" @click="changeCounter">Counter</Button>
			</div>
        </div>
    </div>
</template>

<script>
import comA from './comA.vue'
import comB from './comB.vue'
import comC from './comC.vue'
import {mapGetters} from 'vuex'
export default {
    data(){
        return{
            
        }
    },
    computed : {
        ...mapGetters(['getCounter'])
    },
    methods : {
        changeCounter(){
            this.$store.commit('changeTheCounter', 1)
        }
    },
    created(){
        console.log(this.$store.state.counter)
    },
    components : {
        comA,
        comB,
        comC,
    }
}
</script>

