<template>
  <v-chart
    :option="option"
    style="min-height: 400px;"
  />
</template>

<script>
import {use} from 'echarts/core'
import {CanvasRenderer} from 'echarts/renderers'
import {PieChart} from 'echarts/charts'
import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
} from 'echarts/components'
import VChart from 'vue-echarts'
import {ref} from 'vue'

use([
  CanvasRenderer,
  PieChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
])

export default {
  name: 'TestTest',
  components: {
    VChart,
  },
  setup() {
    const option = ref({
      tooltip: {
        trigger: 'item',
        formatter: '{a} <br/>{b} : {c} ({d}%)',
      },
      legend: {
        orient: 'vertical',
        left: 'left',
        data: ['Przetwarzane', 'Wykorzystane', 'Pozostałe'],
      },
      color: [
        '#2C466F',
        '#8CA7D1',
        '#527ABA',
      ],
      series: [
        {
          name: 'Urlop wypoczynkowy',
          type: 'pie',
          data: [
            {value: 8, name: 'Wykorzystane'},
            {value: 5, name: 'Przetwarzane'},
            {value: 12, name: 'Pozostałe'},
          ],
          center: ['50%', '60%'],
          radius: ['40%', '70%'],
          itemStyle : {
            normal : {
              borderRadius: 10,
              borderColor: '#fff',
              borderWidth: 2,
              label : {
                position: 'inner',
                formatter : params => params.value,
                fontWeight: 'bold',
                fontSize: 16,
                color: '#FFFFFF',
              },
              labelLine : {
                show : false,
              },
            },
          },
        },
      ],
    })

    return {option}
  },
}
</script>
