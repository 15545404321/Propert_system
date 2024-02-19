Vue.component('Update', {
	template: `
		<el-drawer title="修改"  direction="rtl" size="100px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="钥匙名称" prop="ysfl_id">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.ysfl_id" :options="ysfl_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择钥匙名称"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="领用时间" prop="ys_lingyong">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.ys_lingyong" clearable placeholder="请输入领用时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="领用人员" prop="ys_user">
							<el-input  v-model="form.ys_user" autoComplete="off" clearable  placeholder="请输入领用人员"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="使用状态" prop="ys_state">
							<el-radio-group v-model="form.ys_state">
								<el-radio :label="1">使用</el-radio>
								<el-radio :label="2">归还</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="归还时间" prop="ys_ghtime">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.ys_ghtime" clearable placeholder="请输入归还时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="领用备注" prop="ys_beizhu">
							<el-input  type="textarea" autoComplete="off" v-model="form.ys_beizhu"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入领用备注"/>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" style="text-align:center;margin:0 0 30px 0">
				<el-button :size="size" style="width:35%;" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" style="width:35%;" @click="closeForm">取 消</el-button>
			</div>
			</div>
		</el-drawer>
	`
	,
	components:{
		'treeselect':VueTreeselect.Treeselect,
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
				shop_id:'',
				xqgl_id:'',
				ys_lingyong:curentTime(),
				ys_user:'',
				ys_state:1,
				ys_ghtime:'',
				ys_beizhu:'',
			},
			ysfl_ids:[],
			loading:false,
			rules: {
				ys_state:[
					{required: true, message: '使用状态不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Ysly/getFieldList').then(res => {
					if(res.data.status == 200){
						this.ysfl_ids = res.data.data.ysfl_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			if(this.info.pid == '0' ){
				this.$delete(this.info,'pid')
			}
			this.form.ys_lingyong = parseTime(this.form.ys_lingyong)
			this.form.ys_ghtime = parseTime(this.form.ys_ghtime)
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Ysly/update',this.form).then(res => {
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
		normalizer(node) {
			if (node.children && !node.children.length) {
				delete node.children
			}
			return {
				id: node.val,
				label: node.key,
				children: node.children
			}
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
