Vue.component('Jia', {
	template: `
		<el-dialog title="本期数量" width="500px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" label-width="70px">
				<el-row >
					<el-col :span="24">
						<el-form-item label="本期数量" prop="cbgl_bqsl">
							<el-input  v-model="form.cbgl_bqsl" autoComplete="off" clearable  placeholder="请输入本期数量"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
		</el-dialog>
	`
	,
	components:{
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
			},
			member_ids:[],
			fcxx_ids:[],
			louyu_ids:[],
			yblx_ids:[],
			ybzl_ids:[],
			loading:false,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Cbgl/getFieldList').then(res => {
					if(res.data.status == 200){
						this.member_ids = res.data.data.member_ids
						this.fcxx_ids = res.data.data.fcxx_ids
						this.louyu_ids = res.data.data.louyu_ids
						this.yblx_ids = res.data.data.yblx_ids
						this.ybzl_ids = res.data.data.ybzl_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Cbgl/jia',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
