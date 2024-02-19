Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">资产类型：</td>
						<td>
							{{form.zclx_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">楼宇/单元：</td>
						<td>
							{{form.louyu_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">房产名称：</td>
						<td>
							{{form.fcxx_fjbh}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">车位资产：</td>
						<td>
							{{form.cewei_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开始日期：</td>
						<td>
							{{parseTime(form.start_time,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结束日期：</td>
						<td>
							{{parseTime(form.end_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">住户类型：</td>
						<td>
							{{form.khlx_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">住户属性：</td>
						<td>
								{{formatStr(form.glzcgl_type,'[{"key":"主住户","val":"1","label_color":"primary"}]')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">操作人员：</td>
						<td>
							{{form.cname}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">操作时间：</td>
						<td>
							{{parseTime(form.glzcgl_time,'{y}-{m}-{d}')}}
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
			axios.post(base_url+'/Glzcgl/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
