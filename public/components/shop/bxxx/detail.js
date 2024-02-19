Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">报修描述：</td>
						<td>
							{{form.bxxx_miaoshu}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">图片信息：</td>
						<td>
						<div v-if="form.bxxx_pic && form.bxxx_pic.indexOf('[{') != -1" class="demo-image__preview">
							<el-image style="margin-right:5px" v-for="(item,i) in JSON.parse(form.bxxx_pic)"  class="table_list_pic" :src="item.url" :key="i"  :preview-src-list="[item.url]"></el-image>
						</div>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">报修时间：</td>
						<td>
							{{parseTime(form.bxxx_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">所属业主：</td>
						<td>
							{{form.member_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">工程师傅：</td>
						<td>
							{{form.cname}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">处理反馈：</td>
						<td>
							{{form.bxxx_fankui}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">处理时间：</td>
						<td>
							{{parseTime(form.bxxx_cltime)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">客户评分：</td>
						<td>
			<el-rate disabled v-if="form.bxxx_pingfen" v-model="form.bxxx_pingfen"></el-rate>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">客户评价：</td>
						<td>
							{{form.bxxx_pingjia}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">处理状态：</td>
						<td>
							<span v-if="form.bxxx_start == '1'">待处理</span>
							<span v-if="form.bxxx_start == '2'">已处理</span>
							<span v-if="form.bxxx_start == '3'">未处理</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">问题分类：</td>
						<td>
							{{form.bxfl_id}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	`
	,
	props: {
		show: {
			type: Boolean,
			default: true
		},
		size: {
			type: String,
			default: 'mini'
		},
		info: {
			type: Object,
		},
	},
	data() {
		return {
			form:{
			},
		}
	},
	methods: {
		open(){
			axios.post(base_url+'/Bxxx/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
